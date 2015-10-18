<?php
namespace Eardish\Bridge\Controllers;

use Eardish\Bridge\Agents\AnalyticsAgent;
use Eardish\Bridge\Controllers\Core\AbstractController;

class AnalyticController extends AbstractController
{
    protected $analyticsAgent;

    public function __construct(AnalyticsAgent $analyticsAgent)
    {
        $this->analyticsAgent = $analyticsAgent;
    }

    public function submitEntry($requestId)
    {
        $this->kernel->setRequests([
            function () use ($requestId) {
                $this->analyticsAgent->submitEntry($this->dataBlock, $requestId);
            },
            function ($response, $previousIndex) use ($requestId) {
                if ($response['data'][$previousIndex]['success'] == false) {
                    $this->reportError('failed submitting analytics event');
                }

                return $this->reportSuccess();
            }
        ], $requestId);

        $this->kernel->first($requestId);
    }
}