<?php

namespace Eardish\DatabaseService\DatabaseControllers\Models\Traits;

use Doctrine\ORM\Mapping;

trait Timestamp
{
    /**
     * @var \datetime $date_created
     *
     * @Column(type="datetime", nullable = true)
     */
    private $date_created;

    /**
     * @var \datetime $date_modified
     *
     * @Column(type="datetime", nullable = true)
     */
    private $date_modified;


    /**
     * Get date_created
     *
     * @return \datetime
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date_created
     *
     * @param \datetime $date_created
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
    }

    /**
     * Get date_modified
     *
     * @return \datetime
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set date_modified
     *
     * @param \datetime $date_modified
     */
    public function setDateModified($date_modified)
    {
        $this->date_modified = $date_modified;
    }

}
