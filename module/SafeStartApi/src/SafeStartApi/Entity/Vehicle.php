<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use SafeStartApi\Entity\User;

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
        $this->checkLists = new \Doctrine\Common\Collections\ArrayCollection();
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->creation_date = new \DateTime();
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
    protected $responsibleUsers;

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
     * @ORM\Column(type="integer", name="project_number")
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
     * @ORM\Column(type="datetime", name="creation_date")
     */
    protected $creation_date;

    /**
     * @ORM\Column(type="datetime", name="warranty_start_date", nullable=true)
     */
    protected $warranty_start_date;

    /**
     * @ORM\Column(type="float", name="warranty_start_odometer", nullable=true)
     */
    protected $warranty_start_odometer;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = 0;

    /**
     * @ORM\OneToMany(targetEntity="Field", mappedBy="vehicle", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $fields;

    /**
     * @ORM\OneToMany(targetEntity="CheckList", mappedBy="vehicle", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $checkLists;

    /**
     * @ORM\OneToMany(targetEntity="Alert", mappedBy="vehicle", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     */
    protected $alerts;

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

    /**
     * @return array
     */
    public function toInfoArray()
    {
        return array(
            'id' => (!is_null($this->id)) ? $this->id : '',
            'type' => (!is_null($this->type)) ? $this->getType() : '',
            'title' => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            "projectName" => (!is_null($this->getProjectName())) ? $this->getProjectName() : '',
            "projectNumber" => (!is_null($this->getProjectNumber())) ? $this->getProjectNumber() : 0,
            "serviceDueKm" => (!is_null($this->getServiceDueKm())) ? $this->getServiceDueKm() : 0,
            "serviceDueHours" => (!is_null($this->getServiceDueHours())) ? $this->getServiceDueHours() : 0,
            "plantId" => (!is_null($this->getPlantId())) ? $this->getPlantId() : '',
            "registration" => (!is_null($this->getRegistrationNumber())) ? $this->getRegistrationNumber() : '',
            "warrantyStartDate" => $this->getWarrantyStartDate(),
            "warrantyStartOdometer" => $this->getWarrantyStartOdometer(),
            "currentOdometerKms" => (!is_null($this->getCurrentOdometerKms())) ? $this->getCurrentOdometerKms() : 0,
            "currentOdometerHours" => (!is_null($this->getCurrentOdometerHours())) ? $this->getCurrentOdometerHours() : 0,
            "nextServiceDay" => $this->getNextServiceDay(),
            "enabled" => $this->getEnabled(),
        );
    }

    public function getNextServiceDay()
    {
        $date = 'unknown';
        if (!count($this->checkLists)) {
           if ($this->serviceDueHours) {
               $config = \SafeStartApi\Application::getConfig();
               $date = date($config['params']['date_format'], time() + (int) $this->serviceDueHours * 60 * 60);
           }
        } else {
            $averageKms = array();
            $averageHours = array();
            $lastCheckListDate = $this->getCreationDate()->getTimestamp();
            $lastKm = 0;
            $lastHour = 0;
            foreach ($this->checkLists as $checkList) {
                $km = $checkList->getCurrentOdometer() - $lastKm;
                $hours = $checkList->getCurrentOdometerHours() - $lastHour;
                $period = $checkList->getUpdateDate()->getTimestamp() - $lastCheckListDate;
                if ($hours) {
                    $nextServiceSecHours = ($this->serviceDueHours * $period) / $hours;
                    $averageHours[] = $nextServiceSecHours;
                }
                if ($km) {
                    $nextServiceSecKm = ($this->serviceDueKm * $period) / $km;
                    $averageKms[] = $nextServiceSecKm;
                }
                $lastCheckListDate = $checkList->getUpdateDate()->getTimestamp();
                $lastKm = $checkList->getCurrentOdometer();
                $lastHour = $checkList->getCurrentOdometerHours();
            }
            if (!empty($averageKms) || !empty($averageHours)) {
                if(!empty($averageKms)) $averageNextServiceSec1 = round(array_sum($averageKms) / count($averageKms));
                if(!empty($averageHours)) $averageNextServiceSec2 = round(array_sum($averageHours) / count($averageHours));
                if (!empty($averageNextServiceSec2) && !empty($averageNextServiceSec1)) {
                    $averageNextServiceSec = ($averageNextServiceSec1 + $averageNextServiceSec2) / 2;
                } else if (!empty($averageNextServiceSec1)) {
                    $averageNextServiceSec = $averageNextServiceSec1;
                } else if (!empty($averageNextServiceSec2)) {
                    $averageNextServiceSec = $averageNextServiceSec2;
                }
                $config = \SafeStartApi\Application::getConfig();
                $date = date($config['params']['date_format'], time() + $averageNextServiceSec);
            }
        }
        return $date;
    }

    /**
     * @return array
     */
    public function toResponseArray()
    {
        return array(
            'vehicleId' => (!is_null($this->id)) ? $this->id : '',
            'type' => (!is_null($this->type)) ? $this->getType() : '',
            'title' => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            "projectName" => (!is_null($this->getProjectName())) ? $this->getProjectName() : '',
            "projectNumber" => (!is_null($this->getProjectNumber())) ? $this->getProjectNumber() : 0,
            "kmsUntilNext" => (!is_null($this->getServiceDueKm())) ? $this->getServiceDueKm() : 0,
            "hoursUntilNext" => (!is_null($this->getServiceDueHours())) ? $this->getServiceDueHours() : 0,
            "plantId" => (!is_null($this->getPlantId())) ? $this->getPlantId() : '',
            "registration" => (!is_null($this->getRegistrationNumber())) ? $this->getRegistrationNumber() : '',
            "expiryDate" => $this->company->getExpiryDate(),
            "warrantyStartDate" => $this->getWarrantyStartDate(),
            "warrantyStartOdometer" => $this->getWarrantyStartOdometer(),
            "currentOdometerKms" => $this->getCurrentOdometerKms(),
            "currentOdometerHours" => $this->getCurrentOdometerHours(),
        );
    }

    /**
     * @return array
     */
    public function toMenuArray()
    {
        $vehicleData = $this->toInfoArray();
        $vehicleData['text'] = $vehicleData['title'];
        $menuItems = $this->getMenuItems();
        if (empty($menuItems)) $vehicleData['leaf'] = true;
        else $vehicleData['data'] = $menuItems;
        return $vehicleData;
    }

    public function getMenuItems()
    {
        $menuItems = array();
        $user = \SafeStartApi\Application::getCurrentUser();
        if ($user) {
            $menuItems[] = array(
                'id' => $this->getId() . '-info',
                'action' => 'info',
                'text' => 'Current Information',
                'leaf' => true,
            );
            $menuItems[] = array(
                'id' => $this->getId() . '-fill-checklist',
                'action' => 'fill-checklist',
                'text' => 'Daily Inspection',
                'leaf' => true,
            );
            if (count($this->getAlerts()) > 0) {
                $menuItems[] = array(
                    'id' => $this->getId() . '-alerts',
                    'action' => 'alerts',
                    'text' => 'Alerts',
                    'leaf' => true,
                );
            }
            if (count($this->getCheckLists()) > 0) {
                $menuItems[] = array(
                    'id' => $this->getId() . '-inspections',
                    'action' => 'inspections',
                    'text' => 'Inspections',
                    'leaf' => true
                );
            }
            if (count($this->getCheckLists()) > 0) {
                $menuItems[] = array(
                    'id' => $this->getId() . '-report',
                    'action' => 'report',
                    'text' => 'Report',
                    'leaf' => true,
                );
            }
            switch ($user->getRole()) {
                case 'superAdmin':
                case 'companyAdmin':
                case 'companyManager':
                    $menuItems[] = array(
                        'id' => $this->getId() . '-update-checklist',
                        'action' => 'update-checklist',
                        'text' => 'Manage Checklist',
                        'leaf' => true,
                    );
                    $menuItems[] = array(
                        'id' => $this->getId() . '-users',
                        'action' => 'users',
                        'text' => 'Manage Users',
                        'leaf' => true,
                    );
                    break;
            }
        }
        return $menuItems;
    }

    /**
     * @return array
     */
    public function getInspectionsArray()
    {

        $inspections = array();

        $sl = \SafeStartApi\Application::getCurrentControllerServiceLocator();
        $em = $sl->get('Doctrine\ORM\EntityManager');

        $query = $em->createQuery('SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.vehicle = ?1 AND cl.deleted = 0 ORDER BY cl.update_date DESC');
        $query->setParameter(1, $this);
        $items = $query->getResult();

        if (is_array($items) && !empty($items)) {
            foreach ($items as $checkList) {
                $checkListData = array();

                $checkListData['id'] = "checklist-" . $checkList->getId();
                $checkListData['checkListId'] = $checkList->getId();
                $checkListData['checkListHash'] = $checkList->getHash();
                $checkListData['action'] = 'check-list';
                $checkListData['text'] = $checkList->getCreationDate()->format("g:i A d/m/y");
                $checkListData['leaf'] = true;

                $inspections[] = $checkListData;
            }
        }

        return $inspections;
    }

    public function getCurrentOdometerKms()
    {
        $value = 'unknown';
        $lastInspection = $this->getLastInspection();
        if ($lastInspection) {
            $value = $lastInspection->getCurrentOdometer();
        }
        return $value;
    }

    public function getCurrentOdometerHours()
    {
        $value = 'unknown';
        $lastInspection = $this->getLastInspection();
        if ($lastInspection) {
            $value = $lastInspection->getCurrentOdometerHours();
        }
        return $value;
    }

    public function getLastInspection()
    {
        if (!empty($this->checkLists)) {
            return $this->checkLists->last();
        } else {
            return null;
        }
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
     *
     */
    public function removeResponsibleUsers()
    {
        if (!empty($this->responsibleUsers)) return true;
        $cache = \SafeStartApi\Application::getCache();
        foreach ($this->responsibleUsers as $user) {
            $cashKey = "getUserVehiclesList" . $user->getId();
            if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        }
        return $this->responsibleUsers->clear();
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
     *
     */
    public function removeUsers()
    {
        if (!empty($this->users)) return true;
        $cache = \SafeStartApi\Application::getCache();
        foreach ($this->users as $user) {
            $cashKey = "getUserVehiclesList" . $user->getId();
            if ($cache->hasItem($cashKey)) $cache->removeItem($cashKey);
        }
        $this->users->clear();
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
     * Add checklists
     *
     * @param \SafeStartApi\Entity\CheckList $users
     * @return Company
     */
    public function addCheckList(\SafeStartApi\Entity\CheckList $users)
    {
        $this->checkLists[] = $users;

        return $this;
    }

    /**
     * Remove checklists
     *
     * @param \SafeStartApi\Entity\CheckList $users
     */
    public function removeCheckList(\SafeStartApi\Entity\CheckList $users)
    {
        $this->checkLists->removeElement($users);
    }

    /**
     * Get checklists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCheckLists()
    {
        return $this->checkLists;
    }

    /**
     * Remove checklists
     */
    public function removeCheckLists()
    {
        $this->checkLists->clear();
    }

    /**
     * Add alert
     *
     * @param \SafeStartApi\Entity\Alert $alert
     * @return Company
     */
    public function addAlert(\SafeStartApi\Entity\Alert $alert)
    {
        $this->alerts[] = $alert;

        return $this;
    }

    /**
     * Remove alert
     *
     * @param \SafeStartApi\Entity\Alert $alert
     */
    public function removeAlert(\SafeStartApi\Entity\Alert $alert)
    {
        $this->alerts->removeElement($alert);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlerts()
    {
        $alerts = array();
        if (!$this->alerts) return $alerts;
        foreach ($this->alerts as $alert) {
            if (!$alert->getDeleted()) {
                $alerts[] = $alert;
            }
        }
        return $alerts;
    }

    /**
     * Remove alerts
     */
    public function removeAlerts()
    {
        $this->alerts->clear();
    }

    /**
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return Vehicle
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
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return Vehicle
     */
    public function setWarrantyStartDate($creationDate)
    {
        $this->warranty_start_date = $creationDate;

        return $this;
    }

    /**
     * Get creation_date
     *
     * @return \DateTime
     */
    public function getWarrantyStartDate()
    {
        return $this->warranty_start_date ? $this->warranty_start_date->getTimestamp() : null;
    }


    /**
     * Set creation_date
     *
     * @param $creationDate
     * @return Vehicle
     */
    public function setWarrantyStartOdometer($creationDate)
    {
        $this->warranty_start_odometer = $creationDate;

        return $this;
    }

    /**
     * Get creation_date
     *
     * @return float
     */
    public function getWarrantyStartOdometer()
    {
        return $this->warranty_start_odometer;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function haveAccess(User $user)
    {
        if ($this->deleted) return false;

        if ($user->getRole() == 'superAdmin') {
            return true;
        }

        if ($this->users->contains($user) || $this->responsibleUsers->contains($user)) {
            return true;
        }

        $companyAdmin = $this->company->getAdmin();
        if ($user->getId() == $companyAdmin->getId()) {
            return true;
        }

        if($user->getRole() == 'companyManager' && $user->getCompany()->getId() == $this->getCompany()->getId()) {
            return true;
        }

        return false;
    }
}