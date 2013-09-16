<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="checklists")
 *
 */
class CheckList extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     **/
    protected $vehicle;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $hash;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gps_coords;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $current_odometer;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $fields_structure;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $fields_data;

    /**
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="check_list", cascade={"persist", "remove", "merge"})
     */
    protected $alerts;

    /**
     * @ORM\Column(type="datetime", name="creation_date")
     */
    protected $creation_date;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreationDate(new \DateTime());
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
     * Set hash
     *
     * @param string $hash
     * @return CheckList
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set fields_structure
     *
     * @param array $fieldsStructure
     * @return CheckList
     */
    public function setFieldsStructure($fieldsStructure)
    {
        $this->fields_structure = $fieldsStructure;

        return $this;
    }

    /**
     * Get fields_structure
     *
     * @return array
     */
    public function getFieldsStructure()
    {
        return $this->fields_structure;
    }

    /**
     * Set fields_data
     *
     * @param array $fieldsData
     * @return CheckList
     */
    public function setFieldsData($fieldsData)
    {
        $this->fields_data = $fieldsData;

        return $this;
    }

    /**
     * Get fields_data
     *
     * @return array
     */
    public function getFieldsData()
    {
        return $this->fields_data;
    }

    /**
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return CheckList
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
     * Set user
     *
     * @param \SafeStartApi\Entity\User $user
     * @return CheckList
     */
    public function setUser(\SafeStartApi\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \SafeStartApi\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set vehicle
     *
     * @param \SafeStartApi\Entity\Vehicle $vehicle
     * @return CheckList
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
     * Set gps_coords
     *
     * @param string $gpsCoords
     * @return CheckList
     */
    public function setGpsCoords($gpsCoords)
    {
        $this->gps_coords = $gpsCoords;

        return $this;
    }

    /**
     * Get gps_coords
     *
     * @return string
     */
    public function getGpsCoords()
    {
        return $this->gps_coords;
    }

    /**
     * Set current_odometer
     *
     * @param string $gpsCoords
     * @return CheckList
     */
    public function setCurrentOdometer($gpsCoords)
    {
        $this->current_odometer = $gpsCoords;

        return $this;
    }

    /**
     * Get current_odometer
     *
     * @return string
     */
    public function getCurrentOdometer()
    {
        return $this->current_odometer;
    }


    /**
     * Add alerts
     *
     * @param \SafeStartApi\Entity\Alert $alerts
     * @return CheckList
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

    public function getAlertsArray($filters = array())
    {
        $alerts = array();
        if (!empty($this->alerts)) {
            foreach($this->alerts as $alert) {
                //todo: probably we will need more filters here and method should be refactored
                if (isset($filters['status']) && !empty($filters['status'])) {
                    if ($filters['status'] == $alert->getStatus()) {
                        $alerts[] = $alert->toArray();
                    }
                } else {
                    $alerts[] = $alert->toArray();
                }
            }
        }

        return $alerts;
    }
}