<?php
namespace Eardish\DataObjects\Core;

use Eardish\DataObjects\Blocks\ActionBlock;
use Eardish\DataObjects\Blocks\DataBlock;
use Eardish\DataObjects\Blocks\AuthBlock;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\DataObjects\Blocks\RouteBlock;
use Eardish\DataObjects\Blocks\AuditBlock;
use Eardish\DataObjects\Blocks\FollowUpBlock;
use Eardish\DataObjects\Blocks\MessageBlock;
use Eardish\DataObjects\Blocks\StatusBlock;

abstract class AbstractDataObject
{
    /**
     * @var ActionBlock
     */
    protected $actionBlock;
    /**
     * @var DataBlock
     */
    protected $dataBlock;
    /**
     * @var MetaBlock
     */
    protected $metaBlock;
    /**
     * @var AuthBlock
     */
    protected $authBlock;
    /**
     * @var RouteBlock
     */
    protected $routeBlock;
    /**
     * @var AuditBlock
     */
    protected $auditBlock;
    /**
     * @var StatusBlock
     */
    protected $statusBlock;
    /**
     * @var FollowUpBlock
     */
    protected $followUpBlock;
    /**
     * @var MessageBlock
     */
    protected $messageBlock;

    public function __construct(array $blocks)
    {
        foreach ($blocks as $block) {
            $this->injectBlock($block);
        }
    }

    public function injectBlock($block)
    {
        $name = explode("\\", get_class($block));
        $class = array_pop($name);
        $firstLetter = strtolower(substr($class, 0, 1));
        $blockName = $firstLetter . substr($class, 1);

        $this->$blockName = $block;
    }

    public function blockExists($block)
    {
        $blockName = $block . "Block";
        if ($this->$blockName) {
            return true;
        } else {
            return false;
        }
    }

    public function addException($exception)
    {
        if (!$this->auditBlock) {
            $this->auditBlock = new AuditBlock();
        }

        $this->auditBlock->addException($exception);
    }

    public function addNotice($message)
    {
        if (!$this->auditBlock) {
            $this->auditBlock = new AuditBlock();
        }

        $this->auditBlock->addNotice($message);
    }

    public function getLog()
    {
        if (!$this->auditBlock) {
            $this->auditBlock = new AuditBlock();
        }

        return $this->auditBlock->getLog();
    }

    protected function buildIfSet(&$arrayCollector, $blockGetters)
    {
        foreach ($blockGetters as $key => $blockGetter) {
            if (!is_null($blockGetter)) {
                $arrayCollector[$key] = $blockGetter;
            }
        }
    }

    /**
     * @return ActionBlock
     */
    public function getActionBlock()
    {
        return $this->actionBlock;
    }

    /**
     * @return DataBlock
     */
    public function getDataBlock()
    {
        return $this->dataBlock;
    }

    /**
     * @return MetaBlock
     */
    public function getMetaBlock()
    {
        return $this->metaBlock;
    }

    /**
     * @return AuthBlock
     */
    public function getAuthBlock()
    {
        return $this->authBlock;
    }

    /**
     * @return RouteBlock
     */
    public function getRouteBlock()
    {
        return $this->routeBlock;
    }

    /**
     * @return AuditBlock
     */
    public function getAuditBlock()
    {
        return $this->auditBlock;
    }

    /**
     * @return StatusBlock
     */
    public function getStatusBlock()
    {
        return $this->statusBlock;
    }

    /**
     * @return FollowUpBlock
     */
    public function getFollowUpBlock()
    {
        return $this->followUpBlock;
    }

    /**
     * @return MessageBlock
     */
    public function getMessageBlock()
    {
        return $this->messageBlock;
    }
}