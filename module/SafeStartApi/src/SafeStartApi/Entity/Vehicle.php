<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vehicles")
 *
 */
class Vehicle extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="VehicleType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     **/
    protected $type;

    /**
     * @ORM\Column(type="string", length=255, name="vehicle_name")
     */
    protected $vehicleName;

    /**
     * @ORM\Column(type="string", length=255, name="project_name")
     */
    protected $projectName;

    /**
     * @ORM\Column(type="string", length=255, name="project_number")
     */
    protected $projectNumber;

    /**
     * @ORM\Column(type="date", name="expiry_date")
     */
    protected $expiryDate;

    /**
     * @ORM\Column(type="integer", name="kms_until_next")
    */
    protected $kmsUntilNext;

    /**
     * @ORM\Column(type="integer", name="hours_until_next")
    */
    protected $hoursUntilNext;

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

        return array(
            'id'    => (!is_null($this->id)) ? $this->id : '',
            'type'  => (!is_null($this->type)) ? $this->getType()->getTitle() : '',
            'vehicleName' => (!is_null($this->getVehicleName())) ? $this->getVehicleName() : '',
            "projectName" => (!is_null($this->getProjectName())) ? $this->getProjectName() : '',
            "projectNumber" => (!is_null($this->getProjectNumber())) ? $this->getExpiryDate() : '',
            "expiryDate"  => (!is_null($this->getExpiryDate())) ? $this->getExpiryDate() : '',
            "kmsUntilNext" => (!is_null($this->getKmsUntilNext())) ? $this->getKmsUntilNext() : 0,
            "hoursUntilNext" => (!is_null($this->getHoursUntilNext())) ? $this->getHoursUntilNext() : 0,
        );
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
     * Set type
     *
     * @param \SafeStartApi\Entity\VehicleType $type
     * @return Vehicle
     */
    public function setType(\SafeStartApi\Entity\VehicleType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \SafeStartApi\Entity\VehicleType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set vehicleName
     *
     * @param string $vehicleName
     * @return Vehicle
     */
    public function setVehicleName($vehicleName)
    {
        $this->vehicleName = $vehicleName;

        return $this;
    }

    /**
     * Get vehicleName
     *
     * @return string
     */
    public function getVehicleName()
    {
        return $this->vehicleName;
    }

    /**
     * Set projectName
     *
     * @param string $projectName
     * @return Vehicle
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * Get projectName
     *
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * Set projectNumber
     *
     * @param string $projectNumber
     * @return Vehicle
     */
    public function setProjectNumber($projectNumber)
    {
        $this->projectNumber = $projectNumber;

        return $this;
    }

    /**
     * Get projectNumber
     *
     * @return string
     */
    public function getProjectNumber()
    {
        return $this->projectNumber;
    }

    /**
     * Set expiryDate
     *
     * @param \DateTime $expiryDate
     * @return Vehicle
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get expiryDate
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set kmsUntilNext
     *
     * @param integer $kmsUntilNext
     * @return Vehicle
     */
    public function setKmsUntilNext($kmsUntilNext)
    {
        $this->kmsUntilNext = $kmsUntilNext;

        return $this;
    }

    /**
     * Get kmsUntilNext
     *
     * @return integer
     */
    public function getKmsUntilNext()
    {
        return $this->kmsUntilNext;
    }

    /**
     * Set hoursUntilNext
     *
     * @param integer $hoursUntilNext
     * @return Vehicle
     */
    public function setHoursUntilNext($hoursUntilNext)
    {
        $this->hoursUntilNext = $hoursUntilNext;

        return $this;
    }

    /**
     * Get hoursUntilNext
     *
     * @return integer
     */
    public function getHoursUntilNext()
    {
        return $this->hoursUntilNext;
    }
}