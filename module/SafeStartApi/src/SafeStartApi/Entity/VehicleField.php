<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="vehicle_fields")
 *
 */
class VehicleField extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="VehicleField", mappedBy="parent", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="VehicleField", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="field", cascade={"persist", "remove", "merge"})
     */
    protected $alerts;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $alert_title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $alert_description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $trigger_value;

    /**
     * @ORM\Column(type="integer", name="sort_order")
     */
    protected $order;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $default_value;

    /**
     * @ORM\ManyToOne(targetEntity="Vehicle", inversedBy="fields")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id", onDelete="SET NULL")
     **/
    protected $vehicle;

    /**
     * @ORM\Column(type="date", name="creation_date")
     */
    protected $creation_date;

    /**
     * @ORM\Column(type="boolean", name="enabled")
     */
    protected $enabled = 1;

    /**
     * @ORM\Column(type="boolean", name="alert_critical")
     */
    protected $alert_critical = 1;

    /**
     * @ORM\Column(type="boolean", name="deleted")
     */
    protected $deleted;

    /**
     * @ORM\Column(type="boolean", name="additional")
     */
    protected $additional;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", unique=false, onDelete="SET NULL")
     */
    protected $author;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->deleted = false;
        $this->alert_critical = false;
        $this->additional = false;
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreationDate(new \DateTime());
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => (!is_null($this->id)) ? $this->id : '',
            'type' => (!is_null($this->type)) ? $this->getType() : '',
            'title' => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            'description' => (!is_null($this->getDescription())) ? $this->getDescription() : '',
            'text' => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            'sort_order' => (!is_null($this->getOrder())) ? $this->getOrder() : 0,
            'trigger_value' => (!is_null($this->getTriggerValue())) ? $this->getTriggerValue() : '',
            'alert_title' => (!is_null($this->getAlertTitle())) ? $this->getAlertTitle() : '',
            'alert_description' => (!is_null($this->getAlertDescription())) ? $this->getAlertDescription() : '',
            'enabled' => (int)$this->enabled,
            'alert_critical' => (int)$this->alert_critical,
            'default_value' => (!is_null($this->getDefaultValue())) ? $this->getDefaultValue() : '',
            'additional' => (int)$this->additional,
            'default_value' => $this->default_value,
            'parentId' => $this->getParent() ? $this->getParent()->getId() : null,
            'vehicleId' => $this->getVehicle() ? $this->getVehicle()->getId() : null
        );
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
     * Set order
     *
     * @param integer $order
     * @return VehicleField
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
     * Set type
     *
     * @param string $type
     * @return VehicleField
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
     * @return VehicleField
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
     * Set title
     *
     * @param string $title
     * @return VehicleField
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
     * Set title
     *
     * @param string $title
     * @return VehicleField
     */
    public function setDescription($title)
    {
        $this->description = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set alert_title
     *
     * @param string $title
     * @return VehicleField
     */
    public function setAlertTitle($title)
    {
        $this->alert_title = $title;

        return $this;
    }

    /**
     * Get alert_title
     *
     * @return string
     */
    public function getAlertTitle()
    {
        return $this->alert_title;
    }

    /**
     * Set alert_title
     *
     * @param string $title
     * @return VehicleField
     */
    public function setAlertDescription($title)
    {
        $this->alert_description = $title;

        return $this;
    }

    /**
     * Get alert_title
     *
     * @return string
     */
    public function getAlertDescription()
    {
        return $this->alert_description;
    }


    /**
     * Set trigger_value
     *
     * @param string $triggerValue
     * @return VehicleField
     */
    public function setTriggerValue($triggerValue)
    {
        $this->trigger_value = $triggerValue;

        return $this;
    }

    /**
     * Get trigger_value
     *
     * @return string
     */
    public function getTriggerValue()
    {
        return $this->trigger_value;
    }

    /**
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return VehicleField
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
     * @return VehicleField
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
     * @return VehicleField
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
     * @return VehicleField
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
     * Set default_value
     *
     * @param string $defaultValue
     * @return VehicleField
     */
    public function setDefaultValue($defaultValue)
    {
        $this->default_value = $defaultValue;

        return $this;
    }

    /**
     * Get default_value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Add children
     *
     * @param \SafeStartApi\Entity\VehicleField $children
     * @return VehicleField
     */
    public function addChildred(\SafeStartApi\Entity\VehicleField $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \SafeStartApi\Entity\VehicleField $children
     */
    public function removeChildred(\SafeStartApi\Entity\VehicleField $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \SafeStartApi\Entity\VehicleField $parent
     * @return VehicleField
     */
    public function setParent(\SafeStartApi\Entity\VehicleField $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \SafeStartApi\Entity\VehicleField
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add alerts
     *
     * @param \SafeStartApi\Entity\Alert $alerts
     * @return VehicleField
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
     * @return VehicleField
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return VehicleField
     */
    public function setAlertCritical($enabled)
    {
        $this->alert_critical = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getAlertCritical()
    {
        return $this->alert_critical;
    }
}