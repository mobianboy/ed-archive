<?php


class Runner
{

    protected $jobQueue;
    protected $startTime;
    protected $failed;

    function __construct(array $jobQueue)
    {
        $this->$jobQueue = $jobQueue;
        $this->failed = array();
    }

    function run($context)
    {
        $this->startTime = time();
        while(count($this->jobQueue) > 0) {
            foreach ($this->jobQueue as $key => $job) {
                $diffTime = time() - $startTime;
                if (($job->slot()) <= $diffTime) {
                    if ($job->run($context) > 0) {
                        array_push($this->failed, $job);
                    }
                    unset($this->jobQueue[$key]);
                }
            }
            sleep(1);

        }
        return $this->failed;
    }

}

