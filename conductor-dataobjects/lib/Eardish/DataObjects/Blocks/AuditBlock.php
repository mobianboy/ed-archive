<?php
namespace Eardish\DataObjects\Blocks;

class AuditBlock
{
    protected $log = array();
    protected $exception = null;

    public function addException(\Exception $e)
    {
        $this->exception = array("type" => get_class($e), "message" => $e->getMessage(), "code" => $e->getCode());
    }

    public function addNotice($message)
    {
        $this->log[] = array("type" => "notice", "message" => $message);
    }

    public function getLog()
    {
        return $this->log;
    }

    public function hasNotices()
    {
        return !(bool)sizeof($this->log["notice"]);
    }

    public function noticeCount()
    {
        return sizeof($this->log["notice"]);
    }

    public function hasExceptions()
    {
        return (bool)$this->exception;
    }

    public function getException()
    {
        return $this->exception;
    }


    public function __toString()
    {
        $message = '';
        foreach ($this->log as $entry) {
            $message .= '-';
            if ($entry['type'] != 'notice') {
                $message .= $entry['type'] . ' thrown: ' . $entry['message'] . ' with code: ' . $entry['code'];
            } else {
                $message .= $entry['message'];
            }
            $message .= PHP_EOL;
        }

        return trim($message);
    }


}
