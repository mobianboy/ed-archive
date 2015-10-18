<?php
namespace Eardish\Bridge\Controllers\Core;

use Eardish\Bridge\BridgeKernel;
use Eardish\Bridge\Traits\OptionalParams;
use Eardish\Bridge\Traits\ProfileFormatter;
use Eardish\DataObjects\Blocks\MetaBlock;
use Eardish\Exceptions\EDException;
use Eardish\Exceptions\EDPermissionsException;

abstract class AbstractController
{
    use OptionalParams;
    use ProfileFormatter;

    protected $data = array();
    protected $modelType;
    protected $listType;

    /**
     * @var $kernel BridgeKernel
     */
    protected $kernel;

    /**
     * @var $dataBlock array
     */
    protected $dataBlock;

    /**
     * @var MetaBlock
     */
    protected $metaBlock;

    public function reportError($procedure, $code = 10)
    {
        throw new EDException("Controller did not complete successfully. Failed on: ". $procedure, $code);
    }

    public function reportSuccess()
    {
        if (count($this->data)) {
            if ($this->modelType) {
                $success = array('data' => $this->data, 'modelType' => $this->modelType);
            } elseif ($this->listType) {
                $success = array('data' => $this->data, 'listType' => $this->listType);
            } else {
                $success = array('data' => $this->data);
            }
        } else {
            $success = array('data' => array());
        }

        return $success;
    }

    public function setDataBlock($dataBlock)
    {
        $this->dataBlock = $dataBlock;
    }

    public function setMetaBlock($metaBlock)
    {
        $this->metaBlock = $metaBlock;
    }

    public function setModelType($modelType)
    {
        $this->modelType = $modelType;
    }

    public function addInvalidField($name, $error)
    {
        $this->metaBlock->setInvalidFields(
            array(
                'name' => $name,
                'error' => $error
            )
        );
    }

    protected function createArtMap($artResponse)
    {
        foreach($artResponse as $entry => $data) {
            $format = $url = null;
            foreach ($data as $column => $value) {
                if ($column == "format") {
                    $format = $value;
                } elseif ($column == "relative_url") {
                    $url = $value;
                }
                if ($format && $url) {
                    $this->data['art'][$format] = $url;
                }
            }
        }
    }

    public function setKernel(BridgeKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Check to see if the currently logged in user has access to edit the given profileId's data
     *
     * @param $profileId
     * @param null $arRepId
     * @throws EDPermissionsException
     */
    public function checkAccess($profileId, $arRepId = null)
    {
        // Pull the profileId and the profileType from the currently logged in user
        $currentProfile = $this->metaBlock->getCurrentProfile();
        $profileType = $this->metaBlock->getProfileType();
        $arProfile = strpos($profileType, 'ar-');
        // Check to see if a user is trying to edit someone else's profile and they aren't part of ar tools
        if ($currentProfile != $profileId && $arProfile === false) {
            throw new EDPermissionsException("The currently logged in profile does not have permission to access/edit this data.", 42);
        }

        // Check if an ar rep ID was provided by the client
        if ($arRepId) {
            // if the currently logged in AR rep wasn't the one that created the artist and they are not an admin, throw an error
            if (($currentProfile != $arRepId) && ($profileType != "ar-admin")) {
                throw new EDPermissionsException("The currently logged in ar rep does not have edit access to this artist profile.", 42);
            }
        }
    }
}