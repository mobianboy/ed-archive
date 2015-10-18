<?php
namespace Eardish\ImageProcessingService;

use Eardish\ImageProcessingService\Core\Connection;
use Eardish\ImageProcessingService\ImageTools\ImageProcessor;
use Eardish\ImageProcessingService\Core\AbstractService;
use Monolog\Logger;

class ImageProcessingService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;
    protected $aws;

    public function __construct(Connection $connectionService, $kernel, $config)
    {
        parent::__construct($connectionService, $kernel, $config);
        $this->aws = new AWSService($config);
    }

    /**
     * Creates all sizes required for profile pictures
     *
     * @param int $artId
     * @param string $type
     * @param string $imageLocation
     * @param int $profileId
     * @param array $sizes
     * @param string $operation
     * @param string $request
     *
     * @return array
     */
    private function generateAndUpload($artId, $type, $imageLocation, $sizes, $profileId, $operation, $request, $title, $serviceId)
    {
        $tmpFiles = $this->generateTemps($artId, $imageLocation, $sizes);
        $agentResponse = ['conversion-success' => false, 'aws-success' => false, 'db-success' => false];

        if (count($tmpFiles)) {
            $agentResponse['conversion-success'] = true;
        } else {
            return $agentResponse;
        }

        $response = $this->awsUp($type, $profileId, $tmpFiles, $title);
        if ($response) {
            $agentResponse['aws-success'] = true;
            // Prepare and send request to DB.
            $dbRequest = $this->generateConfigArray($request, $operation, $serviceId);
            $dbRequest['multi'] = true;

            $response = $response['images'];

            foreach ($response as $format => $url) {
                $format = explode("_", $format, 3)[2];
                $relativeUrl = explode("/", $url, 6)[5];
                $data[] = ['art_id' => $artId, 'format' => $format, 'url' => $url, 'relative_url' => $relativeUrl];
            }

            $dbRequest['data']['image'] = $data;
            $dbResponse = $this->conn->sendToDB($dbRequest);

            if ($dbResponse['success']) {
                $agentResponse['db-success'] = true;
                // test this delete function.
                //$this->awsDelete($profileId, $imageLocation);
            }
        }

        return $agentResponse;
    }

//    public function addArt($profileId, $type, $sizes, $url, $title = "untitled", $description = "no description", $serviceId)
//    {
//        $dbRequest = $this->generateConfigArray("newArt", "insert", $serviceId);
//        $dbRequest['multi'] = false;
//
//        $dbRequest['data']['art']['profile_id'] = $profileId;
//        $dbRequest['data']['art']['type'] = $type;
//        $dbRequest['data']['art']['title'] = $title;
//        $dbRequest['data']['art']['description'] = $description;
//        $dbRequest['data']['art']['original_url'] = $url;
//
//        $insertArt = $this->conn->sendToDB($dbRequest);
//        //////////////////////////////////////////////
//        if ($insertArt['success']) {
//            $artId = $insertArt['data'][0]['id'];
//            $result = $this->generateAndUpload($artId, $type, $url, $sizes, $profileId, "insert", "addArtImages", $title, $serviceId);
//
//            unset($insertArt);
//        } else {
//            return ['success' => false];
//        }
//
//        if (!$result) {
//            return ['success' => false];
//        }
//
//        $response['success'] = true;
//        $response['artId'] = $artId;
//
//        return $response;
//    }

    public function addArt($profileId, $type, $sizes, $url, $title = "untitled", $description = "no description", $serviceId)
    {
        $self = $this;

        $this->serviceKernel->register([
            function() use ($self, $profileId, $type, $sizes, $url, $title, $description, $serviceId) {
                $dbRequest = $self->generateConfigArray("newArt", "insert", $serviceId);
                $dbRequest['multi'] = false;

                $dbRequest['data']['art']['profile_id'] = $profileId;
                $dbRequest['data']['art']['type'] = $type;
                $dbRequest['data']['art']['title'] = $title;
                $dbRequest['data']['art']['description'] = $description;
                $dbRequest['data']['art']['original_url'] = $url;

                $self->send($dbRequest);
            },
            function($response, $previousIndex) use ($self, $type, $url, $sizes, $profileId, $title, $serviceId) {
                $response = $response['data'][$previousIndex];
                if ($response['success']) {
                    $artId = $response[0]['id'];
                    // Integrate generateAndUpload function here!

                    $tmpFiles = $this->generateTemps($artId, $url, $sizes);
                    $agentResponse = ['conversion-success' => false, 'aws-success' => false, 'db-success' => false];

                    if (count($tmpFiles)) {
                        $agentResponse['conversion-success'] = true;
                    } else {
                        return $agentResponse;
                    }

                    $response = $self->awsUp($type, $profileId, $tmpFiles, $title, $sizes);

                    if ($response) {
                        $agentResponse['aws-success'] = true;
                        // Prepare and send request to DB.
                        $dbRequest = $this->generateConfigArray($request = "addArtImages", $operation = "insert", $serviceId);
                        $dbRequest['multi'] = true;

                        $response = $response['images'];

                        foreach ($response as $format => $url) {
                            $format = explode("_", $format, 3)[2];
                            $relativeUrl = explode("/", $url, 6)[5];
                            $data[] = ['art_id' => $artId, 'format' => $format, 'url' => $url, 'relative_url' => $relativeUrl];
                        }

                        $dbRequest['data']['image'] = $data;
                        $self->send($dbRequest);
                    } else {
                        $self->serviceKernel->selfNext(['serviceId' => $serviceId, 'data' => ['success' => false]]);
                    }
                    $self->serviceKernel->setVariable($serviceId, "artId", $artId);
                    unset($insertArt);
                    // end generate and upload function
                } else {
                    $self->serviceKernel->selfNext(['serviceId' => $serviceId, 'data' => ['success' => false]]);
                }
            },
            function($response, $previousIndex) use ($self, $serviceId) {
                if ($response['data'][$previousIndex]['success']) {
                    $artId = $self->serviceKernel->getVariable($serviceId, "artId");

                    // test this delete function.
                    //$this->awsDelete($profileId, $imageLocation);
                } else {
                    $return['data']['success'] = false;

                    return $return;
                }


                $return['data']['artId'] = $artId;
                $return['data']['success'] = true;

                return $return;
            }
        ], $serviceId);

        $this->serviceKernel->first($serviceId);
    }

    private function generateTemps($profileId, $imageLocation, $sizes)
    {
        $imageProc = new ImageProcessor();
        $tmpFiles = [];
        if ($imageLocation === "default") {
            // TODO link this accounts avatar to the default in the database.
            //$tmpFiles = [];
        } else {
            $imageProc->doImageConv($imageLocation, "url", $profileId, $sizes);

            $tmpFiles = $imageProc->getTmpFiles();
        }

        unset($imageProc);

        return $tmpFiles;
    }

    /**
     * Send data to AWS server.
     *
     * @param array $tmpFiles
     * @param int $profileId
     * @param string $type
     * @return array
     */
    private function awsUp($type, $profileId, $tmpFiles, $title, $sizes)
    {
        // TODO: Implement AWS
        $success = $this->aws->upToS3($type, $profileId, $tmpFiles, $title, $sizes);

        if ($success) {
            return $success;
        } else {
            return array();
        }
    }

    public function awsDelete($profileId, $prefix)
    {
        $this->aws->deleteFromS3($profileId, $prefix);
    }

    /**
     * @param $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
