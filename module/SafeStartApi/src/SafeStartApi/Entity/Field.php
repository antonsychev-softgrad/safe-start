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
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="SET NULL")
     **/
    protected $group;

    /**
     * @ORM\OneToOne(targetEntity="Group", mappedBy="parentField", cascade={"persist", "remove", "merge"})
     */
    protected $subgroup;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="parent")
     */
    protected $additionalFields;

    /**
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="additionalFields")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="field", cascade={"persist", "remove", "merge"})
     */
    protected $alerts;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $value;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $triggerValue;

    /**
     * @ORM\Column(type="integer", name="sort_order")
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     **/
    protected $vehicle;

    /**
     * @ORM\Column(type="date", name="creation_date")
     */
    protected $creation_date;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted;

    /**
     * @ORM\Column(type="boolean", name="additional")
     */
    protected $additional;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->deleted = false;
        $this->additional = false;
        $this->variants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreationDate(new \DateTime());
    }

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
     * @param string $type
     * @return Field
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set vehicle
     *
     * @param \SafeStartApi\Entity\Vehicle $vehicle
     * @return Field
     */
    public function setVehicle(\SafeStartApi\Entity\Vehicle $vehicle = null)
    {
        $this->vehicle = $vehicle;
    
        return $this;
    }

    /**
     * Get vehicle
     *
     * @return \SafeStartApi\Entity\Vehicle 
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Field
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Field
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set triggerValue
     *
     * @param string $triggerValue
     * @return Field
     */
    public function setTriggerValue($triggerValue)
    {
        $this->triggerValue = $triggerValue;
    
        return $this;
    }

    /**
     * Get triggerValue
     *
     * @return string 
     */
    public function getTriggerValue()
    {
        return $this->triggerValue;
    }

    /**
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return Field
     */
    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;
    
        return $this;
    }

    /**
     * Get creation_date
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Field
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set additional
     *
     * @param boolean $additional
     * @return Field
     */
    public function setAdditional($additional)
    {
        $this->additional = $additional;

        return $this;
    }

    /**
     * Get additional
     *
     * @return boolean
     */
    public function getAdditional()
    {
        return $this->additional;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Field
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    
        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Add additionalFields
     *
     * @param \SafeStartApi\Entity\Field $additionalFields
     * @return Field
     */
    public function addAdditionalField(\SafeStartApi\Entity\Field $additionalFields)
    {
        $this->additionalFields[] = $additionalFields;
    
        return $this;
    }

    /**
     * Remove additionalFields
     *
     * @param \SafeStartApi\Entity\Field $additionalFields
     */
    public function removeAdditionalField(\SafeStartApi\Entity\Field $additionalFields)
    {
        $this->additionalFields->removeElement($additionalFields);
    }

    /**
     * Get additionalFields
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdditionalFields()
    {
        return $this->additionalFields;
    }

    /**
     * Set parent
     *
     * @param \SafeStartApi\Entity\Field $parent
     * @return Field
     */
    public function setParent(\SafeStartApi\Entity\Field $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \SafeStartApi\Entity\Field 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add alerts
     *
     * @param \SafeStartApi\Entity\Alert $alerts
     * @return Field
     */
    public function addAlert(\SafeStartApi\Entity\Alert $alerts)
    {
        $this->alerts[] = $alerts;
    
        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \SafeStartApi\Entity\Alert $alerts
     */
    public function removeAlert(\SafeStartApi\Entity\Alert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * Set author
     *
     * @param \SafeStartApi\Entity\User $author
     * @return Field
     */
    public function setAuthor(\SafeStartApi\Entity\User $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \SafeStartApi\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Add subgroup
     *
     * @param \SafeStartApi\Entity\Group $subgroup
     * @return Field
     */
    public function setSubgroup(\SafeStartApi\Entity\Group $subgroup)
    {
        $this->subgroup = $subgroup;
    
        return $this;
    }

    /**
     * Get subgroup
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubgroup()
    {
        return $this->subgroup;
    }
}