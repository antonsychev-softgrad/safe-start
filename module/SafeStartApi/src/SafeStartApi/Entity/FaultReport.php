<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="faultreports")
 *
 */
class FaultReport extends BaseEntity
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $operator_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $gps_coords;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $current_odometer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $current_odometer_hours;

    /**
     * @ORM\Column(type="string", name="current_service_due_kms", nullable=true)
     */
    protected $currentServiceDueKms;

    /**
     * @ORM\Column(type="string", name="current_service_due_hours", nullable=true)
     */
    protected $currentServiceDueHours;

    /**
     * @ORM\Column(type="datetime", name="creation_date")
     */
    protected $creation_date;

    /**
     * @ORM\Column(type="datetime", name="update_date")
     */
    protected $update_date;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = 0;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $location;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCreationDate(new \DateTime());
        $this->setUpdateDate(new \DateTime());
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->creation_date) $this->setCreationDate(new \DateTime());
        $this->setUpdateDate(new \DateTime());
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
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return FaultReport
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
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return FaultReport
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set user
     *
     * @param \SafeStartApi\Entity\User $user
     * @return FaultReport
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
     * Set gps_coords
     *
     * @param string $gpsCoords
     * @return FaultReport
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
     * @param string $value
     * @return FaultReport
     */
    public function setCurrentOdometer($value)
    {
        $this->current_odometer = $value;

        return $this;
    }

    /**
     * Get current_odometer
     *
     * @return string
     */
    public function getCurrentOdometer()
    {
        return $this->current_odometer ? $this->current_odometer : 0;
    }

    /**
     * Get current_odometer_hours
     *
     * @return string
     */
    public function getCurrentOdometerHours()
    {
        return $this->current_odometer_hours ? $this->current_odometer_hours : 0;
    }

    /**
     * Set current_odometer_hours
     *
     * @param $value
     * @internal param string $value
     * @return FaultReport
     */
    public function setCurrentOdometerHours($value)
    {
        $this->current_odometer_hours = $value;

        return $this;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'gps' => $this->getGpsCoords(),
            'location' => $this->getLocation(),
            'operator_name' => $this->getOperatorName(),
            'odometer_kms' => $this->getCurrentOdometer(),
            'odometer_hours' => $this->getCurrentOdometerHours(),
            'serviceDueKm' => $this->getCurrentServiceDueKms(),
            'serviceDueHours' => $this->getCurrentServiceDueHours(),
            'creation_date' => $this->getCreationDate()->getTimestamp(),
            'update_date' => $this->getUpdateDate()->getTimestamp()
        );
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return FaultReport
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
     * Get operator_name
     *
     * @return string
     */
    public function getOperatorName()
    {
        return $this->operator_name;
    }

    /**
     * Set operator_name
     *
     * @param string $value
     * @return FaultReport
     */
    public function setOperatorName($value)
    {
        $this->operator_name = $value;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set location
     *
     * @param $location
     * @return FaultReport
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Set currentServiceDueKms
     *
     * @param string $currentServiceDueKms
     * @return FaultReport
     */
    public function setCurrentServiceDueKms($value)
    {
        $this->currentServiceDueKms = $value;

        return $this;
    }

    /**
     * Get currentServiceDueKms
     *
     * @return string
     */
    public function getCurrentServiceDueKms()
    {
        return $this->currentServiceDueKms ? $this->currentServiceDueKms : 0;
    }

    /**
     * Set currentServiceDueHours
     *
     * @param string $currentServiceDueHours
     * @return FaultReport
     */
    public function setCurrentServiceDueHours($value)
    {
        $this->currentServiceDueHours = $value;

        return $this;
    }

    /**
     * Get currentServiceDueHours
     *
     * @return string
     */
    public function getCurrentServiceDueHours()
    {
        return $this->currentServiceDueHours ? $this->currentServiceDueHours : 0;
    }
}
