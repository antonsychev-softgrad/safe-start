<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="inspection_groups")
 */
class Group extends BaseEntity
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->deleted = false;
        $this->subgroup = false;
        $this->additional = false;
        $this->fields = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="title", unique=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="integer", name="sort_order")
     */
    protected $order;

    /**
     * @ORM\Column(type="boolean", name="additional")
     */
    protected $additional;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="group", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     **/
    protected $fields;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="parent")
     */
    protected $subgroups;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="subgroups")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\OneToOne(targetEntity="Field", inversedBy="subgroup")
     * @ORM\JoinColumn(name="parentfield_id", referencedColumnName="id")
     */
    protected $parentField;

    /**
     * @ORM\ManyToOne(targetEntity="Vehicle", inversedBy="groups")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    protected $vehicle;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    protected $author;

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
     * @ORM\Column(type="boolean", name="subgroup")
     */
    protected $subgroup;

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
     * Set title
     *
     * @param string $title
     * @return Group
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
     * Set order
     *
     * @param integer $order
     * @return Group
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
     * Add fields
     *
     * @param \SafeStartApi\Entity\Field $fields
     * @return Group
     */
    public function addField(\SafeStartApi\Entity\Field $fields)
    {
        $this->fields[] = $fields;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param \SafeStartApi\Entity\Field $fields
     */
    public function removeField(\SafeStartApi\Entity\Field $fields)
    {
        $this->fields->removeElement($fields);
    }

    /**
     * Get fields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set additional
     *
     * @param boolean $additional
     * @return Group
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
     * Set checklist
     *
     * @param \SafeStartApi\Entity\Checklist $checklist
     * @return Group
     */
    public function setChecklist(\SafeStartApi\Entity\Checklist $checklist = null)
    {
        $this->checklist = $checklist;
    
        return $this;
    }

    /**
     * Get checklist
     *
     * @return \SafeStartApi\Entity\Checklist 
     */
    public function getChecklist()
    {
        return $this->checklist;
    }

    /**
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return Group
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
     * @return Group
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Group
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
     * Set subgroup
     *
     * @param boolean $subgroup
     * @return Group
     */
    public function setSubgroup($subgroup)
    {
        $this->subgroup = $subgroup;

        return $this;
    }

    /**
     * Get subgroup
     *
     * @return boolean
     */
    public function getSubgroup()
    {
        return $this->subgroup;
    }

    /**
     * Add subgroups
     *
     * @param \SafeStartApi\Entity\Group $subgroups
     * @return Group
     */
    public function addSubgroup(\SafeStartApi\Entity\Group $subgroups)
    {
        $this->subgroups[] = $subgroups;
    
        return $this;
    }

    /**
     * Remove subgroups
     *
     * @param \SafeStartApi\Entity\Group $subgroups
     */
    public function removeSubgroup(\SafeStartApi\Entity\Group $subgroups)
    {
        $this->subgroups->removeElement($subgroups);
    }

    /**
     * Get subgroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubgroups()
    {
        return $this->subgroups;
    }

    /**
     * Set parent
     *
     * @param \SafeStartApi\Entity\Group $parent
     * @return Group
     */
    public function setParent(\SafeStartApi\Entity\Group $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \SafeStartApi\Entity\Group 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set vehicle
     *
     * @param \SafeStartApi\Entity\Vehicle $vehicle
     * @return Group
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
     * Set author
     *
     * @param \SafeStartApi\Entity\User $author
     * @return Group
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
     * Set parentField
     *
     * @param \SafeStartApi\Entity\Field $parentField
     * @return Group
     */
    public function setParentField(\SafeStartApi\Entity\Field $parentField = null)
    {
        $this->parentField = $parentField;
    
        return $this;
    }

    /**
     * Get parentField
     *
     * @return \SafeStartApi\Entity\Field 
     */
    public function getParentField()
    {
        return $this->parentField;
    }
}