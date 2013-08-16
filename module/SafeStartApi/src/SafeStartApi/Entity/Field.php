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
class Field extends BaseEntity
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
     * @ORM\Column(type="integer")
     */
    protected $typeId;

    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @ORM\Column(type="integer", name="sort_order")
     */
    protected $order;

    /**
     * @ORM\OneToMany(targetEntity="FieldVariant", mappedBy="field", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     **/
    protected $variants;

    /**
     * @ORM\OneToMany(targetEntity="FieldAnswer", mappedBy="field", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     **/
    protected $answers;

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
     * Constructor
     */
    public function __construct()
    {
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Field
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
     * Set order
     *
     * @param integer $order
     * @return Field
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

    /**
     * Set group
     *
     * @param \SafeStartApi\Entity\Group $group
     * @return Field
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
     * @param \SafeStartApi\Entity\FieldType $type
     * @return Field
     */
    public function setType(\SafeStartApi\Entity\FieldType $type = null)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return \SafeStartApi\Entity\FieldType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add variants
     *
     * @param \SafeStartApi\Entity\FieldVariant $variants
     * @return Field
     */
    public function addVariant(\SafeStartApi\Entity\FieldVariant $variants)
    {
        $this->variants[] = $variants;
    
        return $this;
    }

    /**
     * Remove variants
     *
     * @param \SafeStartApi\Entity\FieldVariant $variants
     */
    public function removeVariant(\SafeStartApi\Entity\FieldVariant $variants)
    {
        $this->variants->removeElement($variants);
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * Add answers
     *
     * @param \SafeStartApi\Entity\FieldAnswer $answers
     * @return Field
     */
    public function addAnswer(\SafeStartApi\Entity\FieldAnswer $answers)
    {
        $this->answers[] = $answers;
    
        return $this;
    }

    /**
     * Remove answers
     *
     * @param \SafeStartApi\Entity\FieldAnswer $answers
     */
    public function removeAnswer(\SafeStartApi\Entity\FieldAnswer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnswers()
    {
        return $this->answers;
    }
}