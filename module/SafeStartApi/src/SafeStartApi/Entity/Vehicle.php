<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;

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
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="vehicles")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     **/
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="vehiclesAsigned")
     * @ORM\JoinColumn(name="responsible_user_id", referencedColumnName="id")
     **/
    protected $responsibleUser;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="vehicles")
     * @ORM\JoinTable(name="vehicles_users")
     */
    protected $endUsers;

    /**
     * @ORM\ManyToOne(targetEntity="VehicleType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     **/
    protected $type;

    /**
     * @ORM\Column(type="string", name="plant_id", nullable=false)
     **/
    protected $plantId;

    /**
     * @ ORM \ Column(type="string", name="registration", nullable=false)
     **/
    //protected $registration;

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

    /**
     * Set company
     *
     * @param \SafeStartApi\Entity\Company $company
     * @return Vehicle
     */
    public function setCompany(\SafeStartApi\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \SafeStartApi\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set responsibleUser
     *
     * @param \SafeStartApi\Entity\User $responsibleUser
     * @return Vehicle
     */
    public function setResponsibleUser(\SafeStartApi\Entity\User $responsibleUser = null)
    {
        $this->responsibleUser = $responsibleUser;

        return $this;
    }

    /**
     * Get responsibleUser
     *
     * @return \SafeStartApi\Entity\User
     */
    public function getResponsibleUser()
    {
        return $this->responsibleUser;
    }

    /**
     * Set user
     *
     * @param \SafeStartApi\Entity\User $user
     * @return Vehicle
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
     * Constructor
     */
    public function __construct()
    {
        $this->endUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add endUsers
     *
     * @param \SafeStartApi\Entity\User $endUsers
     * @return Vehicle
     */
    public function addEndUser(\SafeStartApi\Entity\User $endUsers)
    {
        $this->endUsers[] = $endUsers;

        return $this;
    }

    /**
     * Remove endUsers
     *
     * @param \SafeStartApi\Entity\User $endUsers
     */
    public function removeEndUser(\SafeStartApi\Entity\User $endUsers)
    {
        $this->endUsers->removeElement($endUsers);
    }

    /**
     * Get endUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEndUsers()
    {
        return $this->endUsers;
    }

}