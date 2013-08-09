<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="inspection_fields")
 *
 */
class GroupField extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="fields")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     **/
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="GroupFieldType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     **/
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @ORM\Column(type="integer", name="sort_order")
     */
    protected $order;


    protected $variants;



    /**
    * Magic getter to expose protected properties.
    *
    * @param string $property
    * @return mixed
    */
    public function __get($property)
    {
        return $this->$property;
    }

    /**
    * Magic setter to save protected properties.
    *
    * @param string $property
    * @param mixed $value
    */
    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    /**
    * Convert the object to an array.
    *
    * @return array
    */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     */
    public function doStuffOnPrePersist()
    {
        $this->order = (!is_null($this->order)) ? $this->order : 0;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return GroupField
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set group
     *
     * @param \SafeStartApi\Entity\Group $group
     * @return GroupField
     */
    public function setGroup(\SafeStartApi\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \SafeStartApi\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set type
     *
     * @param \SafeStartApi\Entity\GroupFieldType $type
     * @return GroupField
     */
    public function setType(\SafeStartApi\Entity\GroupFieldType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \SafeStartApi\Entity\GroupFieldType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return GroupField
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}