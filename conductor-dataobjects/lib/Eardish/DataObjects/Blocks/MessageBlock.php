<?php
namespace Eardish\DataObjects\Blocks;

class MessageBlock
{
    /**
     * @var array
     */
    protected $messages;

    public function __construct(array $message = array())
    {
        $this->messages = $message;
    }

    /**
     * @param $source string
     * @param $type string
     * @param $content string
     */
    public function addMessage($source, $type, $content)
    {
        $this->messages[] = array("source" => $source, "type" => $type, "content" => $content);
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}