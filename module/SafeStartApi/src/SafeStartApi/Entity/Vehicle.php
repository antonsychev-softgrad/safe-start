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
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->responsibleUsers = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @ORM\ManyToMany(targetEntity="User", inversedBy="responsibleForVehicles")
     * @ORM\JoinTable(name="vehicles_responsible_users")
     **/
    protected $responsibleUser;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="vehicles")
     * @ORM\JoinTable(name="vehicles_users")
     */
    protected $users;

    /**
     * @ORM\Column(type="string", name="type", nullable=true)
     **/
    protected $type;

    /**
     * @ORM\Column(type="string", name="plant_id", unique=true, nullable=false)
     **/
    protected $plantId;

    /**
     * @ORM\Column(type="string", name="registration_number", unique=true, nullable=false)
     **/
    protected $registrationNumber;

    /**
     * @ORM\Column(type="string", length=255, name="title")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, name="project_name")
     */
    protected $projectName;

    /**
     * @ORM\Column(type="string", length=255, name="project_number")
     */
    protected $projectNumber;

    /**
     * @ORM\Column(type="float", name="service_due_km")
     */
    protected $serviceDueHours;

    /**
     * @ORM\Column(type="float", name="service_due_hours")
     */
    protected $serviceDueKm;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = 0;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="vehicle")
     */
    protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="vehicle", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $fields;

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->toInfoArray(), array(
            "users" => array_map(function ($user) {
                return $user->toInfoArray();
            }, (array)$this->users->toArray()),
            "responsibleUsers" => array_map(function ($user) {
                return $user->toInfoArray();
            }, (array)$this->responsibleUsers->toArray()),
        ));
    }

    public function toInfoArray()
    {
        return array(
            'id' => (!is_null($this->id)) ? $this->id : '',
            'type' => (!is_null($this->type)) ? $this->getType() : '',
            'title' => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            "projectName" => (!is_null($this->getProjectName())) ? $this->getProjectName() : '',
            "projectNumber" => (!is_null($this->getProjectNumber())) ? $this->getProjectNumber() : '',
            "serviceDueKm" => (!is_null($this->getServiceDueKm())) ? $this->getServiceDueKm() : 0,
            "serviceDueHours" => (!is_null($this->getServiceDueHours())) ? $this->getServiceDueHours() : 0,
        );
    }

    public function toMenuArray() {
        $vehicleData = $this->toArray();
        $vehicleData['text'] = $vehicleData['title'];
        $menuItems = array();
        $sl = \SafeStartApi\Application::getCurrentControllerServiceLocator();
        if (empty($menuItems)) $vehicleData['leaf'] = true;
        else $vehicleData['items'] = $menuItems;
        return $vehicleData;
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
     * @param string $type
     * @return Vehicle
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
     * Set plantId
     *
     * @param string $plantId
     * @return Vehicle
     */
    public function setPlantId($plantId)
    {
        $this->plantId = $plantId;

        return $this;
    }

    /**
     * Get plantId
     *
     * @return string
     */
    public function getPlantId()
    {
        return $this->plantId;
    }

    /**
     * Set registrationNumber
     *
     * @param string $registrationNumber
     * @return Vehicle
     */
    public function setRegistrationNumber($registrationNumber)
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    /**
     * Get registrationNumber
     *
     * @return string
     */
    public function getRegistrationNumber()
    {
        return $this->registrationNumber;
    }

    /**
     * Set vehicleName
     *
     * @param string $vehicleName
     * @return Vehicle
     */
    public function setTitle($vehicleName)
    {
        $this->title = $vehicleName;

        return $this;
    }

    /**
     * Get vehicleName
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set serviceDueHours
     *
     * @param float $serviceDueHours
     * @return Vehicle
     */
    public function setServiceDueHours($serviceDueHours)
    {
        $this->serviceDueHours = $serviceDueHours;

        return $this;
    }

    /**
     * Get serviceDueHours
     *
     * @return float
     */
    public function getServiceDueHours()
    {
        return $this->serviceDueHours;
    }

    /**
     * Set serviceDueKm
     *
     * @param float $serviceDueKm
     * @return Vehicle
     */
    public function setServiceDueKm($serviceDueKm)
    {
        $this->serviceDueKm = $serviceDueKm;

        return $this;
    }

    /**
     * Get serviceDueKm
     *
     * @return float
     */
    public function getServiceDueKm()
    {
        return $this->serviceDueKm;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Vehicle
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
     * @return Vehicle
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
     * Add responsible
     *
     * @param \SafeStartApi\Entity\User $responsible
     * @return Vehicle
     */
    public function addResponsibleUser(\SafeStartApi\Entity\User $responsible)
    {
        $this->responsibleUsers[] = $responsible;

        return $this;
    }

    /**
     * Remove responsible
     *
     * @param \SafeStartApi\Entity\User $responsible
     */
    public function removeResponsibleUser(\SafeStartApi\Entity\User $responsible)
    {
        $this->responsibleUsers->removeElement($responsible);
    }

    /**
     * Get responsible
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponsibleUsers()
    {
        return $this->responsibleUsers;
    }

    /**
     * Add users
     *
     * @param \SafeStartApi\Entity\User $users
     * @return Vehicle
     */
    public function addUser(\SafeStartApi\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \SafeStartApi\Entity\User $users
     */
    public function removeUser(\SafeStartApi\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add fields
     *
     * @param \SafeStartApi\Entity\Field $fields
     * @return Vehicle
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
     * Add groups
     *
     * @param \SafeStartApi\Entity\Group $groups
     * @return Vehicle
     */
    public function addGroup(\SafeStartApi\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \SafeStartApi\Entity\Group $groups
     */
    public function removeGroup(\SafeStartApi\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }
}