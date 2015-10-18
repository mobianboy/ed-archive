<?php
namespace Eardish\Bridge\Controllers;

use Eardish\Bridge\Agents\AnalyticsAgent;
use Eardish\Bridge\Agents\EmailAgent;
use Eardish\Bridge\Controllers\Core\AbstractController;

class SocialController extends AbstractController
{
    protected $analyticsAgent;
    protected $emailAgent;
    protected $chartName;
    protected $weekStart;
    protected $weekEnd;

    public function __construct(AnalyticsAgent $analyticsAgent, EmailAgent $emailAgent)
    {
        $this->analyticsAgent = $analyticsAgent;
        $this->emailAgent = $emailAgent;
    }

    public function listCharts($requestId)
    {
        $this->chartName = $this->dataBlock['chartName'];

        $day = date('w');
        $weekStart = new \DateTime(date('Y-m-d', strtotime('-' . $day . ' days')));
        $this->weekStart = $weekStart->format('c');
        $weekEnd = new \DateTime(date('Y-m-d', strtotime('+' . (7 - $day) . ' days')));
        $weekEnd = $weekEnd->modify('-1 second');
        $this->weekEnd = $weekEnd->format('c');

        // call function for specific chart
        switch ($this->chartName) {
            case "completed-listens-fan":
                $this->completedListensFan($requestId);
                break;
            case "completed-listens-artist":
                $this->completedListensArtist($requestId);
                break;
            case "completed-listens-track":
                $this->completedListensTrack($requestId);
                break;
            case "most-tracks-rated-fan":
                $this->mostTracksRatedFan($requestId);
                break;
            case "highest-rated-artist":
                $this->highestRatedArtist($requestId);
                break;
            case "highest-rated-track":
                $this->highestRatedTrack($requestId);
                break;
            default:
                $this->reportError('no known chart named: ' . $this->chartName);
        }
    }

    private function getAllCharts()
    {

        $charts = [
            'completed-listens-fan' => $this->completedListensFan('completed-listens-fan'),
            'most-tracks-rated-fan' => $this->mostTracksRatedFan('most-tracks-rated-fan'),
            'completed-listens-track' => $this->completedListensTrack('completed-listens-track'),
            'highest-rated-track' => $this->highestRatedTrack('highest-rated-track')
        ];

        return $charts;
    }

    /**
     * Used to get the top leaders of a chart, even if there is a tie
     *
     * @param $charts
     * @return array
     */
    private function getChartLeaders($charts)
    {
        $leaders = [];
        foreach ($charts as $chartName => $list) {
            $chartToppers = $list['data']['leaderboard'];
            if ($chartToppers) {
                $topValue = $chartToppers[0]['value'];
                $leaders[$chartName] = [];
                foreach ($chartToppers as $chartTopper) {
                    if ($chartTopper['value'] >= $topValue) {
                        array_push($leaders[$chartName], $chartTopper['id']);
                    }
                }
            }
        }

        return $leaders;
    }

    public function distributeBadges($requestId)
    {
        $day = date('w');
        $weekStart = new \DateTime(date('Y-m-d', strtotime('-' . (6 + $day) . ' days')));
        $this->weekStart = $weekStart->format('c');
        $weekEnd = new \DateTime(date('Y-m-d', strtotime('+' .  (1 - $day) . ' days')));
        $weekEnd = $weekEnd->modify('-1 second');
        $this->weekEnd = $weekEnd->format('c');

        $this->kernel->setRequests([
            function () use ($requestId) {
                $this->analyticsAgent->getMostTracksRatedFans($this->weekStart, $this->weekEnd, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                $result = $response['data'][$previousIndex];
                array_pop($result);

                if (!$response['data'][$previousIndex]) {
                    $this->reportError("Failed getting chart: Most Tracks Rated Fans");
                }

                $chartName = 'most-tracks-rated-fan';
                $chart['data']['leaderboard'] = $result;
                $chart['data']['chartName'] = $chartName;
                $chart['data']['dateEnds'] = $this->weekEnd;
                $this->data[$chartName] = $chart;

                $this->analyticsAgent->getCompletedListensFans($this->weekStart, $this->weekEnd, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                $result = $response['data'][$previousIndex];
                array_pop($result);

                if (!$response['data'][$previousIndex]) {
                    $this->reportError("Failed getting chart: Completed Listens Fan");
                }

                $chartName = 'completed-listens-fan';
                $chart['data']['leaderboard'] = $result;
                $chart['data']['chartName'] = $chartName;
                $chart['data']['dateEnds'] = $this->weekEnd;
                $this->data[$chartName] = $chart;

                $this->analyticsAgent->getCompletedListensChart($this->weekStart, $this->weekEnd, $groupBy = 'track_id', $requestId);

            },
            function ($response, $previousIndex) use ($requestId) {
                $result = $response['data'][$previousIndex];
                array_pop($result);

                if (!$response['data'][$previousIndex]) {
                    $this->reportError("Failed getting chart: Completed Listens Track");
                }

                $chartName = 'completed-listens-track';
                $chart['data']['leaderboard'] = $result;
                $chart['data']['chartName'] = $chartName;
                $chart['data']['dateEnds'] = $this->weekEnd;
                $this->data[$chartName] = $chart;

                $this->analyticsAgent->getHighestRatedChart($this->weekStart, $this->weekEnd, $groupBy = 'track_id', $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                $result = $response['data'][$previousIndex];
                array_pop($result);

                if (!$response['data'][$previousIndex]) {
                    $this->reportError("Failed getting chart: Highest rated Track");
                }

                $chartName = 'highest-rated-track';
                $chart['data']['leaderboard'] = $result;
                $chart['data']['chartName'] = $chartName;
                $chart['data']['dateEnds'] = $this->weekEnd;
                $this->data[$chartName] = $chart;

                $charts = $this->data;
                $leaders = $this->getChartLeaders($charts);
                $this->kernel->setVariable($requestId, 'leaders', $leaders);

                if ($leaders) {
                    $this->analyticsAgent->getBadges($requestId);
                } else {
                    $this->reportError("Failed getting chart leaders");
                }
            },
            function ($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->reportError("Failed getting badges");
                }

                $badges = $response['data'][$previousIndex];
                $winners = [];
                $leaders = $this->kernel->getVariable($requestId, 'leaders');
                foreach ($leaders as $chart => $leader) {
                    foreach ($leaders[$chart] as $leader) {
                        if (strpos($chart, "fan")) {
                            $type = "profile";
                        } else {
                            $type = "track";
                        }
                        $winners[] = [
                            "badge_id" => $badges[$chart]['id'],
                            $type."_id" => $leader
                        ];
                    }
                }
                $this->analyticsAgent->distributeBadges($winners, $requestId); // response
            },
            function ($response, $previousIndex) use ($requestId) {
                if (!$response['data'][$previousIndex]) {
                    $this->data['success'] = false;
                    $this->reportError("Failed distributing badges");
                }

                $result = $response['data'][$previousIndex];
                $result['success'] = true;
                $this->data = $result;
                $day = date('w');
                $weekStart = new \DateTime(date('Y-m-d', strtotime('-' . (6 + $day) . ' days')));
                $this->weekStart = $weekStart->format('l, jS \of F Y h:i:s A');
                $weekEnd = new \DateTime(date('Y-m-d', strtotime('+' .  (1 - $day) . ' days')));
                $weekEnd = $weekEnd->modify('-1 second');
                $this->weekEnd = $weekEnd->format('l, jS \of F Y h:i:s A');
                $this->emailAgent->sendBadgeWinnerList(["cesar@eardish.com"], $result, $this->weekStart, $this->weekEnd, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }


    private function completedListensFan($requestId, $chartName = null)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $this->analyticsAgent->getCompletedListensFans($this->weekStart, $this->weekEnd, $requestId);
            },
            function ($response, $previousIndex) use ($chartName) {
                if ($this->chartName) {
                    $chartName = $this->chartName;
                }
                unset($response['data'][$previousIndex]['success']);
                $this->data['leaderboard'] = $response['data'][$previousIndex];
                $this->data['chartName'] = $chartName;
                $this->data['dateEnds'] = $this->weekEnd;

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    private function completedListensArtist($requestId, $chartName = null)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $groupBy = "artist_id";
                $this->analyticsAgent->getCompletedListensChart($this->weekStart, $this->weekEnd, $groupBy, $requestId);
            },
            function ($response, $previousIndex) use ($chartName) {
                if ($this->chartName) {
                    $chartName = $this->chartName;
                }
                unset($response['data'][$previousIndex]['success']);
                $this->data['leaderboard'] = $response['data'][$previousIndex];
                $this->data['chartName'] = $chartName;
                $this->data['dateEnds'] = $this->weekEnd;

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    private function completedListensTrack($requestId, $chartName = null)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $groupBy = "track_id";
                $this->analyticsAgent->getCompletedListensChart($this->weekStart, $this->weekEnd, $groupBy, $requestId);
            },
            function ($response, $previousIndex) use ($chartName) {
                if ($this->chartName) {
                    $chartName = $this->chartName;
                }
                unset($response['data'][$previousIndex]['success']);
                $this->data['leaderboard'] = $response['data'][$previousIndex];
                $this->data['chartName'] = $chartName;
                $this->data['dateEnds'] = $this->weekEnd;

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    private function mostTracksRatedFan($requestId, $chartName = null)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $this->analyticsAgent->getMostTracksRatedFans($this->weekStart, $this->weekEnd, $requestId);
            },
            function ($response, $previousIndex) use ($chartName) {
                if ($this->chartName) {
                    $chartName = $this->chartName;
                }
                unset($response['data'][$previousIndex]['success']);
                $this->data['leaderboard'] = $response['data'][$previousIndex];
                $this->data['chartName'] = $chartName;
                $this->data['dateEnds'] = $this->weekEnd;

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    private function highestRatedArtist($requestId, $chartName = null)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $groupBy = "profile_id";
                $this->analyticsAgent->getHighestRatedChart($this->weekStart, $this->weekEnd, $groupBy, $requestId);
            },
            function ($response, $previousIndex) use ($chartName) {
                if ($this->chartName) {
                    $chartName = $this->chartName;
                }
                unset($response['data'][$previousIndex]['success']);
                $this->data['leaderboard'] = $response['data'][$previousIndex];
                $this->data['chartName'] = $chartName;
                $this->data['dateEnds'] = $this->weekEnd;

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }

    private function highestRatedTrack($requestId, $chartName = null)
    {
        $this->kernel->setRequests([
            function() use ($requestId) {
                $groupBy = "track_id";
                $this->analyticsAgent->getHighestRatedChart($this->weekStart, $this->weekEnd, $groupBy, $requestId);
            },
            function ($response, $previousIndex) use ($chartName) {
                if ($this->chartName) {
                    $chartName = $this->chartName;
                }
                unset($response['data'][$previousIndex]['success']);
                $this->data['leaderboard'] = $response['data'][$previousIndex];
                $this->data['chartName'] = $chartName;
                $this->data['dateEnds'] = $this->weekEnd;

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }
}