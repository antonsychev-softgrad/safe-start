<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(name="inspection_breakdowns")
 */
class InspectionBreakdown extends BaseEntity
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
     * @ORM\Column(name="`key`", type="string", length=256)
     */
    protected $key;

    /**
     * @ORM\Column(type="datetime", name="date", nullable=true)
     */
    protected $date;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $additional = 0;

    /**
     * @ORM\Column(name="default_field", type="boolean")
     */
    protected $default = 0;

    /**
     * @ORM\ManyToOne(targetEntity="CheckList", inversedBy="inspectionBreakdowns")
     * @ORM\JoinColumn(name="check_list_id", referencedColumnName="id")
     **/
    protected $check_list;


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


    public function setFieldId($val)
    {
        $this->field_id = $val;

        return $this;
    }

    public function setAdditional($val)
    {
        $this->additional = $val;

        return $this;
    }

    public function setDefault($val)
    {
        $this->default = $val;

        return $this;
    }

    /**
     * Set check_list
     *
     * @param \SafeStartApi\Entity\CheckList $checkList
     * @return Alert
     */
    public function setCheckList(\SafeStartApi\Entity\CheckList $checkList = null)
    {
        $this->check_list = $checkList;

        return $this;
    }

    /**
     * Get check_list
     *
     * @return \SafeStartApi\Entity\CheckList
     */
    public function getCheckList()
    {
        return $this->check_list;
    }

    /**
     * @return array
     */
    public function toArray() {
        return array(
            'key' => $this->key,
            'field_id' => $this->field_id,
            'additional' => $this->additional,
            'default' => $this->default,
            'date' => $this->date->getTimestamp()
        );
    }
}
