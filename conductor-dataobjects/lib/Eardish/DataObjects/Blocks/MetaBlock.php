<?php
namespace Eardish\DataObjects\Blocks;

class MetaBlock
{
    protected $currentUser;
    protected $currentProfile;
    protected $connId;
    protected $apiVersion;
    protected $responseToken;
    protected $modelType;
    protected $dataSource;
    protected $listType;
    protected $invalidFields;
    protected $fqdn;
    protected $profileType;

    /**
     * @return mixed
     */
    public function getFqdn()
    {
        return $this->fqdn;
    }

    /**
     * @param mixed $fqdn
     */
    public function setFqdn($fqdn)
    {
        $this->fqdn = $fqdn;
    }

    /**
     * @return mixed
     */
    public function getInvalidFields()
    {
        return $this->invalidFields;
    }

    /**
     * @param mixed $invalidFields
     */
    public function setInvalidFields($invalidFields)
    {
        $this->invalidFields[] = $invalidFields;
    }

    /**
     * @return mixed
     */
    public function getListType()
    {
        return $this->listType;
    }

    /**
     * @param mixed $listType
     */
    public function setListType($listType)
    {
        $this->listType = $listType;
    }

    /**
     * @return mixed
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }

    /**
     * @param mixed $currentUser
     */
    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    /**
     * @return mixed
     */
    public function getCurrentProfile()
    {
        return $this->currentProfile;
    }

    /**
     * @param mixed $currentProfile
     */
    public function setCurrentProfile($currentProfile)
    {
        $this->currentProfile = $currentProfile;
    }

    /**
     * @return mixed
     */
    public function getConnId()
    {
        return $this->connId;
    }

    /**
     * @param mixed $connId
     */
    public function setConnId($connId)
    {
        $this->connId = $connId;
    }

    /**
     * @return mixed
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * @param mixed $apiVersion
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    /**
     * @return mixed
     */
    public function getResponseToken()
    {
        return $this->responseToken;
    }

    /**
     * @param mixed $responseToken
     */
    public function setResponseToken($responseToken)
    {
        $this->responseToken = $responseToken;
    }

    /**
     * @return mixed
     */
    public function getModelType()
    {
        return $this->modelType;
    }

    /**
     * @param mixed $modelType
     */
    public function setModelType($modelType)
    {
        $this->modelType = $modelType;
    }

    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param mixed $dataSource
     */
    public function setDataSource($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * @return mixed
     */
    public function getProfileType()
    {
        return $this->profileType;
    }

    /**
     * @param mixed $profileType
     */
    public function setProfileType($profileType)
    {
        $this->profileType = $profileType;
    }
}