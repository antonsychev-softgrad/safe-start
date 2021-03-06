<?php

namespace SafeStartApi\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SafeStartApi\Application as App;
use SafeStartApi\Base\Entity as BaseEntity;
use SafeStartApi\Entity\Alert;
use SafeStartApi\Entity\CheckList;
use SafeStartApi\Entity\Company;
use SafeStartApi\Entity\Field;
use SafeStartApi\Entity\User;

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
     * @ORM\Column(type="string", length=255, name="title", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255, name="project_name", nullable=true)
     */
    protected $projectName;

    /**
     * @ORM\Column(type="string", name="project_number", nullable=true)
     */
    protected $projectNumber;

    /**
     * @ORM\Column(type="float", name="service_due_km", nullable=true)
     */
    protected $serviceDueHours = 0;

    /**
     * @ORM\Column(type="float", name="service_due_hours", nullable=true)
     */
    protected $serviceDueKm = 0;

    /**
     * @ORM\Column(type="float", name="service_threshold_hours", nullable=true)
     */
    protected $serviceThresholdHours = 0;

    /**
     * @ORM\Column(type="float", name="service_threshold_km", nullable=true)
     */
    protected $serviceThresholdKm = 0;

    /**
     * @ORM\Column(type="float", name="current_odometer_hours", nullable=true)
     */
    protected $currentOdometerHours = 0;

    /**
     * @ORM\Column(type="float", name="current_odometer_kms", nullable=true)
     */
    protected $currentOdometerKms = 0;

    /**
     * @ORM\Column(type="float", name="inspection_due_hours", nullable=true)
     */
    protected $inspectionDueHours = 24;

    /**
     * @ORM\Column(type="float", name="inspection_due_kms", nullable=true)
     */
    protected $inspectionDueKms = 500;

    /**
     * @ORM\Column(type="datetime", name="creation_date", nullable=true)
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
     * @ORM\Column(type="datetime", nullable=true, name="expiry_date")
     */
    protected $expiryDate;

    /**
     * @ORM\Column(type="boolean", name="use_kms", nullable=false, options={"default":1})
     */
    protected $useKms = 1;

    /**
     * @ORM\Column(type="boolean", name="use_hours", nullable=false, options={"default":1})
     */
    protected $useHours = 1;

    /**
     * @ORM\Column(type="boolean", name="automatic_sending", nullable=false, options={"default":0})
     */
    protected $automaticSending = 0;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users            = new ArrayCollection();
        $this->responsibleUsers = new ArrayCollection();
        $this->checkLists       = new ArrayCollection();
        $this->alerts           = new ArrayCollection();
        $this->creation_date    = new \DateTime();
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->toInfoArray(), array(
            'users'            => array_map(function ($user) {
                return $user->toInfoArray();
            }, (array)$this->users->toArray()),
            'responsibleUsers' => array_map(function ($user) {
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
            'id'                    => (!is_null($this->id)) ? $this->id : '',
            'type'                  => (!is_null($this->type)) ? $this->getType() : '',
            'title'                 => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            'projectName'           => (!is_null($this->getProjectName())) ? $this->getProjectName() : '',
            'projectNumber'         => (!is_null($this->getProjectNumber())) ? $this->getProjectNumber() : '',
            'serviceDueKm'          => $this->getServiceDueKm(),
            'serviceDueHours'       => $this->getServiceDueHours(),
            'serviceThresholdKm'    => $this->getServiceThresholdKm(),
            'serviceThresholdHours' => $this->getServiceThresholdHours(),
            'plantId'               => (!is_null($this->getPlantId())) ? $this->getPlantId() : '',
            'warrantyStartDate'     => $this->getWarrantyStartDate(),
            'warrantyStartOdometer' => $this->getWarrantyStartOdometer(),
            'currentOdometerKms'    => $this->getCurrentOdometerKms(),
            'currentOdometerHours'  => $this->getCurrentOdometerHours(),
            'nextServiceDay'        => $this->getNextServiceDay(),
            'enabled'               => $this->getEnabled(),
            'expiryDate'            => $this->getExpiryDate(),
            'inspectionDueKms'      => $this->getInspectionDueKms(),
            'inspectionDueHours'    => $this->getInspectionDueHours(),
            'lastInspectionDay'     => $this->getLastInspectionDay(),
            'customFields'          => $this->getCustomFields(),
            'useKms'                => $this->getUseKms(),
            'useHours'              => $this->getUseHours(),
            'automaticSending'      => $this->getAutomaticSending()
        );
    }

    /**
     * @return array
     */
    public function toResponseArray()
    {
        return array(
            'vehicleId'             => (!is_null($this->id)) ? $this->id : '',
            'type'                  => (!is_null($this->type)) ? $this->getType() : '',
            'vehicleName'           => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            'projectName'           => (!is_null($this->getProjectName())) ? $this->getProjectName() : '',
            'projectNumber'         => (!is_null($this->getProjectNumber())) ? $this->getProjectNumber() : '',
            'kmsUntilNext'          => $this->getServiceDueKm(),
            'hoursUntilNext'        => $this->getServiceDueHours(),
            'plantId'               => (!is_null($this->getPlantId())) ? $this->getPlantId() : '',
            'expiryDate'            => $this->getExpiryDate(),
            'restricted'            => $this->company->getRestricted(),
            'currentOdometerKms'    => $this->getCurrentOdometerKms(),
            'currentOdometerHours'  => $this->getCurrentOdometerHours(),
            'serviceThresholdKm'    => $this->getServiceThresholdKm(),
            'serviceThresholdHours' => $this->getServiceThresholdHours(),
            'inspectionDueKms'      => $this->getInspectionDueKms(),
            'inspectionDueHours'    => $this->getInspectionDueHours(),
            'nextServiceDay'        => $this->getNextServiceDay(),
            'lastInspectionDay'     => $this->getLastInspectionDay(),
            'customFields'          => $this->getCustomFields(),
            'automaticSending'      => $this->getAutomaticSending(),
            'useKms'                => $this->getUseKms(),
            'useHours'              => $this->getUseHours(),
        );
    }

    /**
     * @return array
     */
    public function toMenuArray()
    {
        $vehicleData         = $this->toInfoArray();
        $vehicleData['text'] = $vehicleData['plantId'] . ' ' . $vehicleData['title'];
        $menuItems           = $this->getMenuItems();
        if (empty($menuItems)) {
            $vehicleData['leaf'] = true;
        } else $vehicleData['data'] = $menuItems;

        return $vehicleData;
    }

    /**
     * @return array
     */
    public function getMenuItems()
    {
        $menuItems = array();
        $user      = App::getCurrentUser();
        if ($user) {
            $menuItems[] = array(
                'id'     => $this->getId() . '-info',
                'action' => 'info',
                'text'   => 'Current Information',
                'leaf'   => true,
            );
            $menuItems[] = array(
                'id'     => $this->getId() . '-fill-checklist',
                'action' => 'fill-checklist',
                'text'   => 'Perform An Inspection',
                'leaf'   => true,
            );

            if (count($this->getAlerts()) > 0) {
                $menuItems[] = array(
                    'id'      => $this->getId() . '-alerts',
                    'action'  => 'alerts',
                    'text'    => 'Alerts',
                    'counter' => count($this->getOpenAlerts()),
                    'leaf'    => true,
                );
            }
            if (count($this->getCheckLists()) > 0) {
                $menuItems[] = array(
                    'id'     => $this->getId() . '-inspections',
                    'action' => 'inspections',
                    'text'   => 'Previous Inspections',
                    // 'counter' => count($this->getCheckLists()),
                    'badge'  => $this->getCheckListsBadge(),
                    'leaf'   => true
                );
            }
            if (count($this->getCheckLists()) > 0) {
                $menuItems[] = array(
                    'id'     => $this->getId() . '-report',
                    'action' => 'report',
                    'text'   => 'Reporting',
                    'leaf'   => true,
                );
            }
            switch ($user->getRole()) {
                case 'superAdmin':
                case 'companyAdmin':
                case 'companyManager':
                    $menuItems[] = array(
                        'id'     => $this->getId() . '-update-checklist',
                        'action' => 'update-checklist',
                        'text'   => 'Manage Checklists',
                        'leaf'   => true,
                    );
                    $menuItems[] = array(
                        'id'     => $this->getId() . '-users',
                        'action' => 'users',
                        'text'   => 'Manage Users',
                        'leaf'   => true,
                    );
                    $menuItems[] = array(
                        'id'     => $this->getId() . '-update-vehicleField',
                        'action' => 'update-field',
                        'text'   => 'Manage Vehicle Fields',
                        'leaf'   => true,
                    );
                    break;
            }
        }
        return $menuItems;
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
     * Get useKms
     *
     * @return boolean
     */
    public function getUseKms()
    {
        return $this->useKms;
    }

    /**
     * Set useKms
     *
     * @param boolean $useKms
     * @return Vehicle
     */
    public function setUseKms($useKms)
    {
        $this->useKms = $useKms;

        return $this;
    }

    /**
     * Get useHours
     *
     * @return boolean
     */
    public function getUseHours()
    {
        return $this->useHours;
    }

    /**
     * Set useHours
     *
     * @param boolean $useHours
     * @return Vehicle
     */
    public function setUseHours($useHours)
    {
        $this->useHours = $useHours;

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
     * Get expiryDate
     *
     * @return \DateTime
     */
    public function getExpiryDate()
    {
        return $this->expiryDate ? $this->expiryDate->getTimestamp() : (
        $this->company ? $this->company->getExpiryDate() : null
        );
    }

    /**
     * Set expiryDate
     *
     * @param \DateTime $expiryDate
     * @return Company
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getInspectionDueKms()
    {
        return (int)($this->inspectionDueKms ? $this->inspectionDueKms : 500);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInspectionDueKms($value)
    {
        $this->inspectionDueKms = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getInspectionDueHours()
    {
        return (int)($this->inspectionDueHours ? $this->inspectionDueHours : 24);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setInspectionDueHours($value)
    {
        $this->inspectionDueHours = $value;

        return $this;
    }

    /**
     * @return null
     */
    public function getLastInspectionDay()
    {
        $inspection = $this->getLastInspection();
        if ($inspection) {
            return $inspection->getCreationDate()->getTimestamp();
        }

        return null;
    }

    /**
     * @return mixed|null
     */
    public function getLastInspection()
    {
        if (!empty($this->checkLists)) {
            return $this->checkLists->last();
        } else {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getNetServiceDueHours()
    {
        //todo: calculate next service km
        return $this->serviceDueHours;
    }

    /**
     * @return int
     */
    public function getNetServiceDueKms()
    {
        return $this->serviceDueKm;
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
     * Get automaticSending
     *
     * @return boolean
     */
    public function getAutomaticSending()
    {
        return $this->automaticSending;
    }

    /**
     * Set automaticSending
     *
     * @param boolean $automaticSending
     * @return Vehicle
     */
    public function setAutomaticSending($automaticSending)
    {
        $this->automaticSending = $automaticSending;

        return $this;
    }

    /**
     * Add responsible
     *
     * @param User $responsible
     * @return Vehicle
     */
    public function addResponsibleUser(User $responsible)
    {
        $this->responsibleUsers[] = $responsible;

        return $this;
    }

    /**
     * Remove responsible
     *
     * @param User $responsible
     */
    public function removeResponsibleUser(User $responsible)
    {
        $this->responsibleUsers->removeElement($responsible);
    }

    /**
     *
     */
    public function removeResponsibleUsers()
    {
        if (empty($this->responsibleUsers))
            return true;
        $cache = App::getCache();
        $users = clone $this->responsibleUsers;
        foreach ($users as $user) {
            $cashKey = 'getUserVehiclesList' . $user->getId();
            if ($cache->hasItem($cashKey))
                $cache->removeItem($cashKey);
            $this->responsibleUsers->removeElement($user);
        }
        $this->responsibleUsers->clear();
        $this->responsibleUsers = null;

        return true;
    }

    /**
     * Get responsible
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponsibleUsers()
    {
        $users = new ArrayCollection();
        if (!$this->responsibleUsers)
            return $users;
        foreach ($this->responsibleUsers as $user) {
            if (!$user->getDeleted() && $user->getEnabled()) {
                $users->add($user);
            }
        }

        return $users;
    }

    /**
     * Add users
     *
     * @param User $users
     * @return Vehicle
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     *
     */
    public function removeUsers()
    {
        if (empty($this->users))
            return true;
        $cache = App::getCache();
        foreach ($this->users as $user) {
            $cashKey = 'getUserVehiclesList' . $user->getId();
            if ($cache->hasItem($cashKey))
                $cache->removeItem($cashKey);
        }
        $this->users->clear();
        $this->users = null;

        return true;
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        $users = new ArrayCollection();
        if (!$this->users)
            return $users;
        foreach ($this->users as $user) {
            if (!$user->getDeleted() && $user->getEnabled()) {
                $users->add($user);
            }
        }

        return $users;
    }

    /**
     * Add fields
     *
     * @param Field $fields
     * @return Vehicle
     */
    public function addField(Field $fields)
    {
        $this->fields[] = $fields;

        return $this;
    }

    /**
     * Remove fields
     *
     * @param Field $fields
     */
    public function removeField(Field $fields)
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
     * @param CheckList $checklist
     * @return Company
     */
    public function addCheckList(CheckList $checklist)
    {
        $this->checkLists[] = $checklist;

        return $this;
    }

    /**
     * Remove checklists
     *
     * @param CheckList $checklist
     */
    public function removeCheckList(CheckList $checklist)
    {
        $this->checkLists->removeElement($checklist);
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
     * @param Alert $alert
     * @return Company
     */
    public function addAlert(Alert $alert)
    {
        $this->alerts[] = $alert;

        return $this;
    }

    /**
     * Remove alert
     *
     * @param Alert $alert
     */
    public function removeAlert(Alert $alert)
    {
        $this->alerts->removeElement($alert);
    }

    /**
     * Remove alerts
     */
    public function removeAlerts()
    {
        $this->alerts->clear();
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
    public function setCreationDate($creationDate)
    {
        $this->creation_date = $creationDate;

        return $this;
    }

    /**
     * Get company
     *
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set company
     *
     * @param Company $company
     * @return Vehicle
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;

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
     * Get vehicleName
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * Get projectName
     *
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
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
     * Get projectNumber
     *
     * @return string
     */
    public function getProjectNumber()
    {
        return $this->projectNumber;
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
     * Get serviceDueKm
     *
     * @return float
     */
    public function getServiceDueKm()
    {
        return (float)$this->serviceDueKm;
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
     * Get serviceDueHours
     *
     * @return float
     */
    public function getServiceDueHours()
    {
        return (float)$this->serviceDueHours;
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
     * Get serviceThresholdKm
     *
     * @return float
     */
    public function getServiceThresholdKm()
    {
        return (float)$this->serviceThresholdKm;
    }

    /**
     * Set serviceThresholdKm
     *
     * @param float $serviceThresholdKm
     * @return Vehicle
     */
    public function setServiceThresholdKm($serviceThresholdKm)
    {
        $this->serviceThresholdKm = $serviceThresholdKm;

        return $this;
    }

    /**
     * Get serviceThresholdHours
     *
     * @return float
     */
    public function getServiceThresholdHours()
    {
        return (float)$this->serviceThresholdHours;
    }

    /**
     * Set serviceThresholdHours
     *
     * @param float $serviceThresholdHours
     * @return Vehicle
     */
    public function setServiceThresholdHours($serviceThresholdHours)
    {
        $this->serviceThresholdHours = $serviceThresholdHours;

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
     * Set plantId
     *
     * @param string $plantId
     * @return Vehicle
     */
    public function setPlantId($plantId)
    {
        $this->plantId = strtoupper($plantId);

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
     * @return float
     */
    public function getWarrantyStartOdometer()
    {
        return $this->warranty_start_odometer;
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
     * @return float
     */
    public function getCurrentOdometerKms()
    {
        return (float)($this->currentOdometerKms ? $this->currentOdometerKms : 0);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCurrentOdometerKms($value)
    {
        $this->currentOdometerKms = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getCurrentOdometerHours()
    {
        return (float)($this->currentOdometerHours ? $this->currentOdometerHours : 0);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setCurrentOdometerHours($value)
    {
        $this->currentOdometerHours = $value;

        return $this;
    }

    /**
     * Get checklists
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCheckLists()
    {
        $alerts = array();
        if (!$this->checkLists)
            return $alerts;
        foreach ($this->checkLists as $alert) {
            if (!$alert->getDeleted()) {
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlerts()
    {
        $alerts = array();
        if (!$this->alerts)
            return $alerts;
        foreach ($this->alerts as $alert) {
            if (!$alert->getDeleted()) {
                $alerts[] = $alert;
            }
        }

        return $alerts;
    }

    /**
     * Get open alerts
     *
     * @return array
     */
    public function getOpenAlerts()
    {
        $alerts     = $this->getAlerts();
        $openAlerts = array();
        foreach ($alerts as $alert) {
            if ($alert->getStatus() === Alert::STATUS_NEW) {
                $openAlerts[] = $alert;
            }
        }

        return $openAlerts;
    }

    public function getCheckListsBadge()
    {
        $badge             = '';
        $lastInspectionDay = $this->getLastInspectionDay();
        $now               = time();
        $delta             = $now - $lastInspectionDay;
        $days              = floor($delta / 60 / 60 / 24);
        if ($days) {
            $badge = $days . ' Days Since Last';
        }

        return $badge;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        $em    = App::getEntityManager();
        $query = $em->createQuery('SELECT al FROM SafeStartApi\Entity\VehicleField al WHERE al.vehicle = ?1');
        $query->setParameter(1, $this);

        $items = $query->getResult();

        $getFieldsIds = function ($items) {
            $fieldsIds = array();
            foreach ($items as $item) {
                if ($item->getParent() != null) {
                    $fieldsIds[] = $item->getId();
                }
            }

            return $fieldsIds;
        };

        $findParentKey = function ($item, $fieldsIds) use (&$findParentKey) {
            $parentId = $item->getParent() ? $item->getParent()->getId() : null;
            if (!$parentId)
                return null;

            return (in_array($parentId, $fieldsIds)) ? $parentId : $findParentKey($item->getParent(), $fieldsIds);
        };

        $fieldsIds = $getFieldsIds($items);

        $fields = array();
        foreach ($items as $item) {
            $field = array();
            if ($item->getParent() != null) {
                $parentId               = $item->getParent()->getId();
                $field['id']            = $item->getId();
                $field['title']         = $item->getTitle();
                $field['type']          = $item->getType();
                $field['default_value'] = $item->getDefaultValue();
                if (in_array($parentId, $fieldsIds)) {
                    $parentKey                    = $findParentKey($item, array_keys($fields));
                    $fields[$parentKey]['data'][] = $field;
                } else {
                    $fields[$item->getId()] = $field;
                }
            }
        }

        $result = array();
        foreach ($fields as $field) {
            $row                  = array();
            $row['id']            = $field['id'];
            $row['title']         = $field['title'];
            $row['type']          = $field['type'];
            $row['default_value'] = $field['default_value'];
            $result[]             = $row;
            if (isset($field['data'])) {
                $result = array_merge($result, $field['data']);
            }
        }

        //        $fields = array();
        //
        //        foreach($items as $item){
        //            $field = array();
        //            if($item->getParent() != NULL){
        //                $field['id'] = $item->getId();
        //                $field['title'] = $item->getTitle();
        //                $field['type'] = $item->getType();
        //                $field['default_value'] = $item->getDefaultValue();
        //                $fields[] = $field;
        //            }
        //        }

        return $result;
    }

    public function getExportData($from, $to) {

        $config = App::getConfig();
        $humanize = function($world) {
            return ucfirst(preg_replace('~(?<=\\w)([A-Z])|[_-]+~', ' $1', $world));
        };

        $to->modify('+1 day');

        $em = App::getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')
            ->from('SafeStartApi\Entity\CheckList', 'e')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('e.vehicle', ':vehicle'),
                    $qb->expr()->neq('e.deleted', ':deleted'),
                    $qb->expr()->gte('e.update_date', ':from'),
                    $qb->expr()->lte('e.update_date', ':to')))
            ->orderBy('e.update_date', 'DESC')
            ->setParameters(array(
                    'vehicle' => $this,
                    'deleted' => 1,
                    'from'    => $from,
                    'to'      => $to,
                ));
        $items = $qb->getQuery()->getResult();

        $kms   = '-';
        $hours = '-';
        if (count($items) >= 2) {
            $kms   = $items[0]->getCurrentOdometer() - $items[count($items) - 1]->getCurrentOdometer();
            $hours = (int)$items[0]->getCurrentOdometerHours() - (int)$items[count($items) - 1]->getCurrentOdometerHours();
        }

        $vehicleData = array(
            'Plant Id'                 => $this->getPlantId(),
            'Expiry date'              => date($config['params']['date_format'], $this->getExpiryDate()),
            'Current odometer kms'     => $this->getCurrentOdometerKms(),
            'Current odometer hours'   => $this->getCurrentOdometerHours(),
            'Km\'s in selected period' => $kms,
            'Hours in selected period' => $hours,
            'Alerts'                   => '',
            'Model'                    => (!is_null($this->getTitle())) ? $this->getTitle() : '',
            'Make'                     => (!is_null($this->type)) ? $this->getType() : '',
            'Project Name'             => (!is_null($this->getProjectName())) ? $this->getProjectName() : '',
            'Project Number'           => (!is_null($this->getProjectNumber())) ? $this->getProjectNumber() : '',
        );

        $customFields = $this->getCustomFields();
        foreach ($customFields as $k => $f) {
            if((array_key_exists($f['title'], $vehicleData)
                    && $vehicleData[$f['title']] === '')
                || !empty($f['default_value'])) {
                $vehicleData[$f['title']] = $f['default_value'];
            }
        }

        $header = array_keys($vehicleData);
        $alertColPos = array_search('Alerts', $header);
        $header = array_map(function($key) use ($humanize) {
            return $humanize($key);
        }, $header);
        $results[] = $header;

        $alerts = $this->getAlertsByPeriod($from, $to, true);
        if(sizeof($alerts)){
            $i = 0;
            foreach($alerts as $alert) {
                if($i++ == 0) {
                    array_splice($vehicleData, $alertColPos, 1, reset($alert));
                    $results[] = $vehicleData;
                } else {
                    $alert = array_pad($alert, -($alertColPos + 1), '');
                    $results[] = $alert;
                }
            }
        } else {
            $results[] = $vehicleData;
        }

        return $results;
    }

    /**
     * @return bool|string
     */
    public function getNextServiceDay()
    {
        $config = App::getConfig();
        $currentDate = new \DateTime();
        $checkLists = $this->getCheckLists();
        if (sizeof($checkLists) < 2)
            return date($config['params']['date_format'], $currentDate->getTimestamp());

        /* second variant: * /
        $firstCheckList = array_shift($checkLists);
        $lastKms = $firstCheckList->getCurrentOdometer();
        $lastHours = $firstCheckList->getCurrentOdometerHours();
        $lastUpdateDate = $firstCheckList->getUpdateDate()->getTimestamp();

        $serviceDueKms = $this->getNetServiceDueKms();
        $serviceDueHours = $this->getNetServiceDueHours();


        $serviceDaysByHours = array();
        $serviceDaysByKms = array();

        foreach ($checkLists as $checkList) {
            $kms = $checkList->getCurrentOdometer();
            $hours = $checkList->getCurrentOdometerHours();
            $deltaKms = $kms - $lastKms;
            $deltaHours = $hours - $lastHours;
            $updateDate = $checkList->getUpdateDate()->getTimestamp();
            $deltaTime = $updateDate - $lastUpdateDate;
            $kmsLeft = $serviceDueKms - $kms;
            $hoursLeft = $serviceDueHours - $hours;

            if ($deltaKms && $deltaTime) {
                $serviceDaysByKms[] = $kmsLeft / ($deltaKms / $deltaTime) + $updateDate;
            }
            if ($deltaHours && $deltaTime) {
                $serviceDaysByHours[] = $hoursLeft / ($deltaHours / $deltaTime) + $updateDate;
            }

            $lastKms = $kms;
            $lastHours = $hours;
            $lastUpdateDate = $updateDate;
        }

        if (count($serviceDaysByKms)) {
            $averageServiceDateByKms = array_sum($serviceDaysByKms) / count($serviceDaysByKms);
        } else {
            $averageServiceDateByKms = 0;
        }
        if (count($serviceDaysByHours)) {
            $averageServiceDateByHours = array_sum($serviceDaysByHours) / count($serviceDaysByHours);
        } else {
            $averageServiceDateByHours = 0;
        }

        if($averageServiceDateByKms && $averageServiceDateByHours) {
            $serviceDate = min($averageServiceDateByKms, $averageServiceDateByHours);
        } elseif($averageServiceDateByKms) {
            $serviceDate = $averageServiceDateByKms;
        } elseif($averageServiceDateByHours) {
            $serviceDate = $averageServiceDateByHours;
        } else {
            $serviceDate = 0;
        }

        if ($serviceDate !== 0) {
            $config = App::getConfig();
            $date = date($config['params']['date_format'], $serviceDate);
        }
        /* end second variant. */

        /* first variant: * /
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
                 $nextServiceSecHours = ($this->getNetServiceDueHours() * $period) / $hours;
                 $averageHours[] = $nextServiceSecHours;
             }
             if ($km) {
                 $nextServiceSecKm = ($this->getNetServiceDueKms() * $period) / $km;
                 $averageKms[] = $nextServiceSecKm;
             }
             $lastCheckListDate = $checkList->getUpdateDate()->getTimestamp();
             $lastKm = $checkList->getCurrentOdometer();
             $lastHour = $checkList->getCurrentOdometerHours();
         }
         if (!empty($averageKms) || !empty($averageHours)) {
             if (!empty($averageKms)) $averageNextServiceSec1 = round(array_sum($averageKms) / count($averageKms));
             if (!empty($averageHours)) $averageNextServiceSec2 = round(array_sum($averageHours) / count($averageHours));
             if (!empty($averageNextServiceSec2) && !empty($averageNextServiceSec1)) {
                 $averageNextServiceSec = ($averageNextServiceSec1 + $averageNextServiceSec2) / 2;
             } else if (!empty($averageNextServiceSec1)) {
                 $averageNextServiceSec = $averageNextServiceSec1;
             } else if (!empty($averageNextServiceSec2)) {
                 $averageNextServiceSec = $averageNextServiceSec2;
             }
             if (!empty($averageNextServiceSec)) {
                 $config = App::getConfig();
                 $date = date($config['params']['date_format'], time() + $averageNextServiceSec);
             }
         }
        /* end first variant. */


        /* third variant: */
        $firstCheckList = array_shift($checkLists);
        $lastKms        = $firstCheckList->getCurrentOdometer();
        $lastHours      = $firstCheckList->getCurrentOdometerHours();
        $lastUpdateDate = $firstCheckList->getUpdateDate()->getTimestamp();

        $serviceDaysByKms   = array();
        $serviceDaysByHours = array();

        foreach ($checkLists as $checkList) {

            $curKms        = $checkList->getCurrentOdometer();
            $curHours      = $checkList->getCurrentOdometerHours();
            $curUpdateDate = $checkList->getUpdateDate()->getTimestamp();

            $intervalKms        = $curKms - $lastKms;
            $intervalHours      = $curHours - $lastHours;
            $intervalUpdateDate = ($curUpdateDate - $lastUpdateDate);

            if($this->getUseKms()) {
                if ($intervalKms > 0 && $intervalUpdateDate) {
                    $serviceDaysByKms[] = $intervalKms / $intervalUpdateDate;
                }
            }

            if($this->getUseHours()) {
                if ($intervalHours > 0 && $intervalUpdateDate) {
                    $serviceDaysByHours[] = $intervalHours / $intervalUpdateDate;
                }
            }

            $lastKms        = $curKms;
            $lastHours      = $curHours;
            $lastUpdateDate = $curUpdateDate;
        }

        if (count($serviceDaysByKms)) {
            $averageServiceDateByKms = array_sum($serviceDaysByKms) / count($serviceDaysByKms);
            $averageServiceDateByKms = round(($this->getServiceDueKm() - $this->getCurrentOdometerKms()) / $averageServiceDateByKms);
        } else {
            $averageServiceDateByKms = 0;
        }
        if (count($serviceDaysByHours)) {
            $averageServiceDateByHours = array_sum($serviceDaysByHours) / count($serviceDaysByHours);
            $averageServiceDateByHours = round(($this->getServiceDueHours() - $this->getCurrentOdometerHours()) / $averageServiceDateByHours);
        } else {
            $averageServiceDateByHours = 0;
        }

        if ($averageServiceDateByKms > 0 && $averageServiceDateByHours > 0) {
            $serviceDate = min($averageServiceDateByKms, $averageServiceDateByHours);
        } elseif ($averageServiceDateByKms > 0) {
            $serviceDate = $averageServiceDateByKms;
        } elseif ($averageServiceDateByHours > 0) {
            $serviceDate = $averageServiceDateByHours;
        } else {
            $serviceDate = 0;
        }

        $currentDate->add(new \DateInterval(sprintf("PT%dS", (int)$serviceDate)));
        $date   = date($config['params']['date_format'], $currentDate->getTimestamp());

        /* end third variant. */

        return $date;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function getAlertsByPeriod(\DateTime $from = null, \DateTime $to = null, $export = false)
    {
        //if (!$from) $from = new \DateTime(date('Y-m-d', time() - 30 * 24 * 60 * 60));
        //if (!$to) $to = new \DateTime();
        $alerts = array();

        $em    = App::getEntityManager();
        $query = $em->createQuery('SELECT al FROM SafeStartApi\Entity\Alert al WHERE al.vehicle = ?1 AND al.deleted = 0 AND al.update_date >= :from AND al.update_date <= :to  ORDER BY al.update_date DESC');
        $query->setParameter(1, $this)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $items = $query->getResult();
        foreach ($items as $item) {
            if ($export) {
                $alerts[] = $item->toExportArray();
            } else {
                $alerts[] = $item->toArray();
            }
        }

        return $alerts;
    }

    /**
     * @return array
     */
    public function getInspectionsArray()
    {

        $inspections = array();

        $sl = App::getCurrentControllerServiceLocator();
        $em = $sl->get('Doctrine\ORM\EntityManager');

        $query = $em->createQuery('SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.vehicle = ?1 AND cl.deleted = 0 ORDER BY cl.update_date DESC');
        $query->setParameter(1, $this);
        $items = $query->getResult();

        if (is_array($items) && !empty($items)) {
            foreach ($items as $checkList) {
                $checkListData = array();

                $checkListData['id']            = 'checklist-' . $checkList->getId();
                $checkListData['checkListId']   = $checkList->getId();
                $checkListData['checkListHash'] = $checkList->getHash();
                $checkListData['action']        = 'check-list';
                $config                         = App::getConfig();
                $checkListData['text']          = $checkList->getCreationDate()->format($config['params']['date_format'] . ' ' . $config['params']['time_format']);
                $checkListData['leaf']          = true;

                $inspections[] = $checkListData;
            }
        }

        return $inspections;
    }

    public function getPrevInspectionDay()
    {
        if (count($this->checkLists) < 2)
            return null;

        return $this->checkLists[count($this->checkLists) - 2]->getCreationDate()->getTimestamp();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function getStatistic(\DateTime $from = null, \DateTime $to = null)
    {
        if (!$from)
            $from = new \DateTime(date('Y-m-d', time() - 30 * 24 * 60 * 60));
        if (!$to)
            $to = new \DateTime();
        $to->modify('tomorrow');
        $to->modify('1 second ago');

        $em    = App::getEntityManager();
        $query = $em->createQuery('SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.vehicle = ?1 AND cl.deleted = 0 AND cl.update_date >= :from AND  cl.update_date <= :to  ORDER BY cl.update_date DESC');
        $query->setParameter(1, $this)
            ->setParameter('from', $from)
            ->setParameter('to', $to);
        $items = $query->getResult();

        $kms   = 0;
        $hours = 0;
        if (count($items) >= 2) {
            $kms   = $items[0]->getCurrentOdometer() - $items[count($items) - 1]->getCurrentOdometer();
            $hours = (int)$items[0]->getCurrentOdometerHours() - (int)$items[count($items) - 1]->getCurrentOdometerHours();
        }

        $inspections      = count($items);
        $completed_alerts = array();
        $new_alerts       = array();
        $chart            = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $alerts = $item->getAlerts();
                if (!empty($items)) {
                    foreach ($alerts as $alert) {
                        if ($alert->getStatus() == 'new') {
                            $new_alerts[] = $alert;
                        } else {
                            $completed_alerts[] = $alert;
                        }
                    }
                }
                $km   = $item->getCurrentOdometer();
                $hour = $item->getCurrentOdometerHours();

                if (!empty($km) && !empty($hour)) {
                    $chart[] = array(
                        'value'         => round($km / $hour),
                        'formattedDate' => date('Y-m-d', $item->getCreationDate()->getTimestamp()),
                        'date'          => $item->getCreationDate()->getTimestamp() * 1000,
                    );
                }
            }
        }

        return array(
            'kms'              => ((int)$kms <= 0) ? '-' : $kms,
            'hours'            => ((int)$hours <= 0) ? '-' : $hours,
            'inspections'      => $inspections,
            'completed_alerts' => count($completed_alerts),
            'new_alerts'       => count($new_alerts),
            'chart'            => $chart
        );
    }

    /**
     * @return array
     */
    public function getCurrentDayOdometerUsage()
    {
        $usage = array(
            'kms'   => 0,
            'hours' => 0,
        );

        $em = App::getEntityManager();

        $beginOfDay = strtotime('midnight', time());
        $endOfDay   = strtotime('tomorrow', $beginOfDay);
        $startDay   = strtotime('midnight', time()) - 24 * 60 * 60;
        $from       = new \DateTime(date('Y-m-d', $beginOfDay));
        $start      = new \DateTime(date('Y-m-d', $startDay));
        $to         = new \DateTime(date('Y-m-d', $endOfDay));

        $query = $em->createQuery('SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.vehicle = ?1 AND cl.deleted = 0 AND cl.update_date >= :start AND cl.update_date < :from ORDER BY cl.update_date DESC');
        $query->setParameter(1, $this)
            ->setParameter('from', $from)
            ->setParameter('start', $start)
            ->setMaxResults(1);
        $items = $query->getResult();

        if (!count($items)) {
            $query = $em->createQuery('SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.vehicle = ?1 AND cl.deleted = 0 AND cl.update_date >= :from AND  cl.update_date <= :to  ORDER BY cl.update_date DESC');
            $query->setParameter(1, $this)
                ->setParameter('from', $from)
                ->setParameter('to', $to);
            $items = $query->getResult();

            if (count($items) < 2)
                return $usage;

            $lastCheckList  = $items[0];
            $firstCheckList = $items[count($items) - 1];
        } else {
            $firstCheckList = $items[0];
            $query          = $em->createQuery('SELECT cl FROM SafeStartApi\Entity\CheckList cl WHERE cl.vehicle = ?1 AND cl.deleted = 0 AND cl.update_date >= :from AND  cl.update_date <= :to  ORDER BY cl.update_date DESC');
            $query->setParameter(1, $this)
                ->setParameter('from', $from)
                ->setParameter('to', $to);
            $items = $query->getResult();

            if (!count($items))
                return $usage;
            $lastCheckList = $items[0];
        }

        $kms2  = $lastCheckList->getCurrentOdometer();
        $kms1  = $firstCheckList->getCurrentOdometer();
        $h1    = $firstCheckList->getCurrentOdometerHours();
        $h2    = $lastCheckList->getCurrentOdometerHours();
        $usage = array(
            'kms'   => ($kms2 - $kms1),
            'hours' => ($h2 - $h1),
        );

        return $usage;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function haveAccess(User $user)
    {
        if ($this->deleted)
            return false;

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

        if ($user->getRole() == 'companyManager' && $user->getCompany()->getId() == $this->getCompany()->getId()) {
            return true;
        }

        return false;
    }

    public function getInspectionBreakdowns($from = null, $to = null, $range = 'monthly')
    {
        $chart = array();
        if ($range == 'monthly') {
            $delta = 30 * 24 * 60 * 60;
        } else $delta = 7 * 24 * 60 * 60;
        if (!$from)
            $from = new \DateTime(date('Y-m-d', time() - $delta * 6));
        if (!$to)
            $to = new \DateTime();

        $em = App::getEntityManager();

        $fromTime = $from->getTimestamp();
        $toTime   = $to->getTimestamp();

        while ($fromTime < $toTime) {
            if ($range == 'monthly') {
                $date = date('m/Y', $fromTime);
            } else  $date = date('W/Y', $fromTime);

            $toTimeParam = new \DateTime();
            $toTimeParam->setTimestamp($fromTime + $delta);
            $fromTimeParam = new \DateTime();
            $fromTimeParam->setTimestamp($fromTime);

            $dql   = 'SELECT COUNT(cl.id) FROM SafeStartApi\Entity\CheckList cl WHERE cl.deleted = 0 AND cl.creation_date >= :from AND  cl.creation_date <= :to AND cl.user is not null AND cl.vehicle = (:vehicle)';
            $query = $em->createQuery($dql);
            $query->setParameter('from', $fromTimeParam)->setParameter('to', $toTimeParam)->setParameter('vehicle', $this);
            $value1   = $query->getSingleScalarResult();
            $fromTime = $fromTime + $delta;
            $chart[]  = array(
                'date'   => $date,
                'value1' => $value1,
            );
        }

        return $chart;
    }
}
