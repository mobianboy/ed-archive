<?php
namespace Eardish\ImageProcessingService;

use Aws\Common\Aws;
use Aws\S3\S3Client;
// This file originally pulled from the Alpha-MFP
// Things that were stripped from the original file: entities and an entity manager, setting up the environment (dev versus production for the base urls) and AWS CloudFront

class AWSService
{
    /**
     * S3 Client
     *
     * @var S3Client
     */
    protected $s3;

    /**
     * AWS Bucket
     *
     * @var string
     */
    protected $bucket;

    /**
     * AWS Region
     *
     * @var string
     */
    protected $region;

    /**
     * @param \Eardish\AppConfig $config
     *
     * @throws CredentialsException
     */
    public function __construct($config)
    {
        $this->region = $config->get('imageprocessing.aws.region');
        $this->bucket = $config->get('imageprocessing.aws.bucket');
        $awsConfigRaw = file_get_contents($config->get('aws-path'));
        $awsConfig = json_decode($awsConfigRaw, true);
        if ($awsConfig['imageprocessing']['key'] && $awsConfig['imageprocessing']['id']) {
            $aws = Aws::factory(array(
                'profile' => 'default',
                'region'  => $this->region,
                'key' => $awsConfig['imageprocessing']['id'],
                'secret' => $awsConfig['imageprocessing']['key'],

            ));
            $this->s3 = $aws->get('s3');
        } else {
            throw new CredentialsException("AWS Key(s) environment variables are not set. Terminating.");
        }
    }

    /**
     * @param $type
     * @param $profileId
     * @param array $tmpFiles
     * @param $title
     * @return array
     */
    public function upToS3($type, $profileId, array $tmpFiles, $title, $sizes)
    {
        $urls = array();

        foreach ($tmpFiles as $imageInfo) {
            list($typeId, $fileName) = explode("_", $imageInfo['file'], 2);
            // phone_small.jpg, tablet_large.jpg etc
            $fileType = explode("_", $fileName, 3);
            // phone_small, tablet_large, etc
            $fileExtension = explode(".", $fileName)[1];
            $format = explode(".", $fileType[2])[0];
            if ($format != "original") {
                $size = $sizes["profile_art_".$format];
                $format = $size . "x" . $size;
            }
            list($columnName) = explode(".", $fileName, 2);

            $s3Path = "public/".$profileId."/".$typeId."/".$format.".".$fileExtension;

            $imageResource = fopen($imageInfo['path'], 'r');
            // Push to s3 and get back their result object
            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $s3Path,
                'Body' => $imageResource
            ]);
//            $result = $this->s3->upload($this->bucket, $s3Path, $imageResource, 'public-read');
            if (is_resource($imageResource)) {
                fclose($imageResource);
            }

            // Add the url of the image to urls array
            $images[$columnName] = $result->offsetGet('ObjectURL');

            // Clean up
            unlink($imageInfo['path']);
            unset($val, $result, $imageResource);
        }

        $urls['images'] = $images;

        return $urls;
    }

    // This probably also belongs in the image processor class. Try to keep this AWS class doing only AWS stuff.


    public function deleteFromS3($userId, $prefix)
    {
        // deletes all images for a given userid, need to use regex for more specific deletes
        $this->s3->deleteMatchingObjects($this->bucket, $userId, $prefix);
    }
}
