<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(name="inspection_changes")
 */
class InspectionChanges extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     **/
    protected $field_id;

    /**
 * @ORM\Column(type="integer", nullable=true)
 **/
    protected $company_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     **/
    protected $user_id;

    /**
     * @ORM\Column(name="`company_name`", type="string", length=1000, nullable=true)
     */
    protected $company_name;

    /**
     * @ORM\Column(name="`user_name`", type="string", length=1000, nullable=true)
     */
    protected $user_name;

    /**
     * @ORM\Column(name="`key`", type="string", length=1000, nullable=true)
     */
    protected $key;

    /**
     * @ORM\Column(name="`prev_key`", type="string", length=1000, nullable=true)
     */
    protected $prev_key = '';

    /**
     * @ORM\Column(name="`action`", type="string", length=256, nullable=true)
     */
    protected $action;

    /**
     * @ORM\Column(name="`type`", type="string", length=256, nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime", name="date", nullable=true)
     */
    protected $date;


    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->date = new \DateTime();
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return Data
     */
    public function setKey($entity)
    {
        $this->key = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    public function setPrevKey($entity)
    {
        $this->prev_key = $entity;

        return $this;
    }

    public function setAction($val)
    {
        $this->action = $val;

        return $this;
    }

    public function setType($val)
    {
        $this->type = $val;

        return $this;
    }

    public function setFieldId($val)
    {
        $this->field_id = $val;

        return $this;
    }

    public function setCompanyId($val)
    {
        $this->company_id = $val;

        return $this;
    }

    public function setCompanyName($val)
    {
        $this->company_name = $val;

        return $this;
    }

    public function setUserId($val)
    {
        $this->user_id = $val;

        return $this;
    }

    public function setUserName($val)
    {
        $this->user_name = $val;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array(
            'key' => $this->key,
            'prev_key' => $this->prev_key,
            'field_id' => $this->field_id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'company_id' => $this->company_id,
            'company_name' => $this->company_name,
            'action' => $this->action,
            'type' => $this->type,
            'date' => $this->date->getTimestamp()
        );
    }
}
