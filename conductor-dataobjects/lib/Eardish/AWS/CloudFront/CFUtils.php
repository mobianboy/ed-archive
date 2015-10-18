<?php

namespace Eardish\AWS\CloudFront;

use Aws\CloudFront\CloudFrontClient;

class CFUtils
{
    private $key;
    private $secret;

    public function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    protected function makeBaseURL($path, $mode)
    {
        return 'http://'.$mode['cf'].'/'.$path;
    }

    protected function getCDN($mode)
    {

        $cdn = CloudFrontClient::factory(array(
            'profile'     => 'default',
            'region'      => $mode['region'],
            'bucket'      => $mode['bucket'],
            'version'     => 'latest',
            'credentials' => array(
                'key' => $this->key,
                'secret' => $this->secret
            )
        ));

        return $cdn;
    }

    public function makeExpiringURL($path, $mode, $duration)
    {
        $cdn = $this->getCDN($mode);

        $seconds = $duration*60;

        $url = $cdn->getSignedUrl(array(
            'url' => $this->makeBaseURL($path, $mode),
            'expires' => $seconds
        ));

        return $url;
    }

    public function makeStaticURL($path, $mode)
    {
        return $this->makeBaseURL($path, $mode);
    }

    public function invalidateAsset($paths, $mode)
    {
        $cdn = $this->getCDN($mode);

        $result = $cdn->createInvalidation(array(
            'DistributionId' => $mode['distid'],
            'Paths' => [
                'Quantity' => count($paths),
                'Items' => $paths
            ],
            'CallerReference' => str_replace('.', '', microtime(true))
        ));

        $status = $result->get('Status');

        return ($status === 'InProgress');
    }
}