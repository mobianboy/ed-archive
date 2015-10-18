<?php
namespace Eardish\SongIngestionService;

use Eardish\Exceptions\EDConnectionException;
use Eardish\Exceptions\EDConnectionReadException;
use Eardish\Exceptions\EDConnectionWriteException;
use Eardish\Exceptions\EDInvalidOrMissingParameterException;
use Eardish\Exceptions\EDTransportException;
use Eardish\SongIngestionService\Core\AbstractService;
use Aws\S3\S3Client;

use Monolog\Logger;

class SongIngestionService extends AbstractService
{
    /**
     * @var Logger
     */
    protected $logger;
    /*
     * Database Port number
     */
    protected $port;

    /*
     * Database host location
     */
    protected $addr;

    /*
     * S3 instance
     */
    protected $s3;

    protected $originId = "S3-eardish.dev.songs/track";

    protected $originalBaseDir = "/home/kryptyk/testbed/";
    protected $destinationBaseDir = "/home/kryptyk/testbed/outputs/";

    ///////////////  transcoder operations

    public function transcodeAudioToAllFormats($trackId, $startClip = "0", $endClip = "-1")
    {

        // STEP 1 - stage track locally - response is the full path to the file
        $original = $this->transcodeStageTrack($trackId);
        if(!($original)) {
            throw new EDInvalidOrMissingParameterException("unable to find original file for track $trackId");
        }

        // STEP 2 - iterate through each of the business driven targets
        //
        // 'AllFormats' (yes, i know, this is business logic in services,
        // but this will have to do for now)

        $targets = ["LOW"];

        foreach ($targets as $target) {
            $res = $this->transcode($trackId, $original, $target, $startClip, $endClip);
            sleep(1);       // space it out a bit
        }
    }

    /**
     * called by the transcoder when it has finished a transcoding job
     * @param $trackId
     * @param $transcodingJobId -
     */
    public function transcodeFinishJob($jobId, $filename, $path)
    {
        $datetime = new \DateTime();

        // call the finish job method for the transcoder
        $job = $this->generateConfigArray("transcodeFinishJob", 'update');
        $job["data"]["transcode_job"]["id"] = $jobId;
        $job["data"]["transcode_job"]["status"] = "TRANSCODE COMPLETE";
        $job["data"]["transcode_job"]["finished_on"] = $datetime->format('Y-m-d H:i:s');

        $this->conn->sendToDB($job);


        // clean up after transcoder
        $this->transcodePushAndCleanFile($jobId, $filename, $path);

        $datetime = new \DateTime();

        // call the finish job method for the transcoder
        $job = $this->generateConfigArray("transcodePushedJob", 'update');
        $job["data"]["transcode_job"]["id"] = $jobId;
        $job["data"]["transcode_job"]["status"] = "PUSHED";
        $job["data"]["transcode_job"]["pushed_on"] = $datetime->format('Y-m-d H:i:s');

        $this->conn->sendToDB($job);

        //////////////////////////////////
        // register the file in the db
        //
        // i'm cheating a bit to get the trackId and format -- just to prevent another db call

        $trackId            = strtok($filename,'.');
        $targetFormat       = strtok('.');

        $enc                = "MP3";       // just some default val
        $br                 = 128;
        // i'm also cheating some more here -- to get the encoding type and bitrate from the 'format'

        switch($targetFormat) {
            case "HIGH":
                $enc = "MP3";
                $br = 320;
                break;
            case "LOW":
                $enc = "MP3";
                $br = 128;
                break;
            case "CLIP":
                $enc = "MP3";
                $br = "128";
                break;
            default:
                // TODO - BLOW UP GRACEFULLY
        }
        // register the audio file (add it to the audio table)
        $this->transcodeRegisterAudioFile($trackId, $filename, $targetFormat, $enc, $br );
    }

    //////////

    /**
     * given a trackId, transcodes the original file to the      //  $config['data']['transcodeJob']['id'] =


    //  return $this->conn->sendToDB($config); targetFormat
     * @param $trackId
     * @param string $targetFormat
     */
    protected function transcode($trackId, $original, $target, $startClip = "0", $endClip = "-1" )
    {
        $params = array();

        $params["track-id"] = $trackId;
        $params["target"]   = $target;
        $params["start"]    = $startClip;
        $params["end"]      = $endClip;

        // pull the original from S3 and put it in the proper place in the local FS

        // check to see if the file is there (once S3 has time to xfer)
        // if no, then create an exception and return it

        // set the format and bitrate
        switch($target) {
            case ("LOW"):
            case("CLIP"):
                $params["encoding"] = "MP3";
                $params["bitrate"]  = 128;
                break;
            case ("HIGH"):
                $params["encoding"] = "MP3";
                $params["bitrate"]  = 320;
                break;
            case ("LOSSLESS"):
                $params["encoding"] = "FLAC";
                $params["bitrate"]  = 0;
                break;
            default:
                throw new EDInvalidOrMissingParameterException("unknown encoding target '$target'");
        }

        $enc = $params["encoding"];

        $params["infile"]  = $original;
        // build the target name
        $params["outfile"] = "$trackId.$target.$enc";
        $params["infilebasedir"] = $this->originalBaseDir;
        $params["outfilebasedir"] = $this->destinationBaseDir;

        // record job and get a jobId to start it with
        $params["job-id"] = (int)$this->transcodeStartJob($params);

        // send request  transcoder
        $resp = $this->transcodeRunJob($params);

        /////////////////////////////////////////////////////
        //   that's it --
        //   transcoder will respond by calling
        //   the transcodeFinishJob method
        //

        return 0;
    }


    /////////////////////////////////////////////////////////////////////////////
    ///// transcoder helpers

    /**
     * the actual network call to start a transcoder job
     * @param $data
     */
    protected function transcodeRunJob($data)
    {
        $json = json_encode($data);

        // open socket to transcoder
        $fp = stream_socket_client($this->transcoderURL.':'.$this->transcoderPort);
        if(!($fp)) {
            throw new EDConnectionException("cannot connect to transcoder at $this->transcoderURL port $this->transcoderPort");
        }
        stream_set_blocking($fp,0);
        if(!(fwrite($fp, $json))) {     // json_encode($request));
            throw new EDConnectionWriteException("cannot write to transcoder at $this->transcoderURL port $this->transcoderPort");
        }
        $response = stream_get_contents($fp);

        fclose($fp);
        return json_decode($response, true);
    }


    /**
     * sets up the track locally for the transcoder
     * @param $trackId
     */
    public function transcodeStageTrack($trackId)
    {
        // get the S3 URL from the DB
        $config = $this->generateConfigArray(__FUNCTION__, 'select');

        // TODO -- THIS SHOULD PROBABLY CHANGE TO 'TARGET' - FORMAT IS SOMETHING ELSE
        $config["data"]["audio"]["format"] = "'ORIGINAL'";
        $config["data"]["audio"]["track_id"] = $trackId;

        $resp = $this->conn->sendToDB($config);

        // this should be the s3 url we are looking
        $filename = $resp["data"]["0"]["url"];

        if(!($filename)) {
            throw new EDInvalidOrMissingParameterException("SONG INGESTION: missing or null filename in transcodeStageTrack()");
        }

        // NOTE:  we might not be able to access track URLs this way
        // TODO -- DO WE NEED TO AUTH IN S3 HERE?  WE SHOULD.
        // TODO -- HASH SOURCE FILENAMES
        // uncomment and implement hashed original filenames after SIS is stablized
        // this will keep from overwriting the same files if there are overlapping requests
//        $hashedTargetFilename = sha1($filename);  // DO SOMETHING ABOUT RANDOMIZATION

        $target = $this->originalBaseDir . "/$filename";
        $url = "http://$this->bucket.s3.amazonaws.com/track/$trackId/$filename";

        // copy it to the staging directory
        // TODO -- this might not be the most efficient way to do this
        if(!(copy($url, $target ))) {
            throw new EDTransportException("unable to copy file from S3 to local for transcoding");
        }
        // return URL
        return $filename;

    }

    /**
     * records the job in the database and gets a transcoderJob id
     * @param array
     * @return integer
     */
    protected function transcodeStartJob($params)
    {
        $datetime = new \DateTime();


        $job = $this->generateConfigArray(__FUNCTION__, 'insert');

        // call the db to start the job, return jobId
        $job["data"]["transcode_job"]["track_id"]   = $params["track-id"];
        $job["data"]["transcode_job"]["target"]     = $params["target"];
        //  $job["data"]["transcode_job"]["start"]      = $params["start"];
        //  $job["data"]["transcode_job"]["end"]        = $params["end"];
        $job["data"]["transcode_job"]["encoding"]   = $params["encoding"];
        $job["data"]["transcode_job"]["bitrate"]    = $params["bitrate"];
        $job["data"]["transcode_job"]["started_on"] = $datetime->format('Y-m-d H:i:s');

        $job["data"]["transcode_job"]["status"]     = "RUNNING";

        $resp = $this->conn->sendToDB($job);

        // pull the job_id out of the response
        return $resp["data"][0]['id'];

    }

    /**
     * pushes the transcoder results to S3 and removes the files from the local filesystem
     */
    public function transcodePushAndCleanFile($jobId, $filename, $path)
    {
        // get a client to S3
        $this->s3 = new S3Client(array(
            'credentials' => array(
                'profile' => 'default',
                'key' => $this->accessKey,
                'secret' => $this->secretKey
            ),
            'Bucket' => $this->bucket,
            'region' => $this->region,
            // AWS PHP SDK version 3 requires an AWS API version...in order to preserve backwards compatibility we might want to hard code an actual version here
            'version' => 'latest',
            'debug' => false,
            'retries' => 5
        ));


        $trackId = strstr($filename, '.', true);
        $fpath = $path . "$filename";


        // expecting a full path filename from the transcoder
        $audioFile = fopen($fpath, 'r');

       // die("BUCKET BUCKET BUCKET BUCKET : $this->bucket s3url: $s3url audiofile: $audioFile");

        // TODO -- ADD PROPER EXCEPTION HANDLING HERE -- AMAZON DOESN'T MAKE IT EASY
        // .. TO SEE WHAT KIND OF RESULT WE'D GET BACK HERE

        $result = $this->s3->upload($this->bucket, "track/$trackId/$filename", $audioFile,'public-read');


        fclose($audioFile);
        unlink($fpath);
    }



    public function transcodeRegisterAudioFile($trackId, $s3url, $targetFormat, $encoding, $bitrate)
    {
        $job = $this->generateConfigArray(__FUNCTION__, 'insert');

        // call the db to start the job, return jobId
        $job["data"]["audio"]["track_id"]   = $trackId;
        $job["data"]["audio"]["url"]        = $s3url;
        $job["data"]["audio"]["format"]     = $targetFormat;
        $job["data"]["audio"]["encoding"]   = $encoding;
        $job["data"]["audio"]["bitrate"]    = $bitrate;

        $resp = $this->conn->sendToDB($job);

        return $resp;

    }
    // TODO a start/restart for xcoderd maybe??



}
