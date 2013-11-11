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
    // reduced odometer data
    const WARNING_DATA_DISCREPANCY_KMS = 'date_discrepancy_kms';
    const WARNING_DATA_DISCREPANCY_HOURS = 'date_discrepancy_hours';

    // 24h/500km per day
    const WARNING_DATA_INCORRECT = 'date_incorrect';

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
    protected $pdf_link;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $operator_name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fault_pdf_link;

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
     * @ORM\OneToMany(targetEntity="DefaultAlert", mappedBy="check_list", cascade={"persist", "remove", "merge"})
     */
    protected $default_alerts;

    /**
     * @ORM\OneToMany(targetEntity="InspectionBreakdown", mappedBy="check_list", cascade={"persist", "remove", "merge"})
     */
    protected $inspectionBreakdowns;

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
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $user_data;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     *
     * PHP array using json_encode() and json_decode()
     */
    protected $warnings = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $email_mode = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreationDate(new \DateTime());
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
     * Get pdf_link
     *
     * @return string
     */
    public function getPdfLink()
    {
        return $this->pdf_link;
    }

    /**
     * Set pdf_link
     *
     * @param string $pdf_link
     * @return CheckList
     */
    public function setPdfLink($pdf_link)
    {
        $this->pdf_link = $pdf_link;

        return $this;
    }

    /**
     * Get pdf_link
     *
     * @return string
     */
    public function getFaultPdfLink()
    {
        return $this->fault_pdf_link;
    }

    /**
     * Set pdf_link
     *
     * @param string $pdf_link
     * @return CheckList
     */
    public function setFaultPdfLink($pdf_link)
    {
        $this->fault_pdf_link = $pdf_link;

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
     * Set fields_data
     *
     * @param array $fieldsData
     * @return CheckList
     */
    public function setUserData($fieldsData)
    {
        $this->user_data = $fieldsData;

        return $this;
    }

    /**
     * Get fields_data
     *
     * @return array
     */
    public function getUserData()
    {
        return $this->user_data;
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
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return CheckList
     */
    public function setUpdateDate($creationDate)
    {
        $this->update_date = $creationDate;

        return $this;
    }

    /**
     * Get creation_date
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
     * Get current_odometer
     *
     * @return string
     */
    public function getCurrentOdometerHours()
    {
        return $this->current_odometer_hours ? $this->current_odometer_hours : 0;
    }

    /**
     * Set current_odometer
     *
     * @param $value
     * @internal param string $gpsCoords
     * @return CheckList
     */
    public function setCurrentOdometerHours($value)
    {
        $this->current_odometer_hours = $value;

        return $this;
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
     * Add alerts
     *
     * @param \SafeStartApi\Entity\Alert|\SafeStartApi\Entity\DefaultAlert $alerts
     * @return CheckList
     */
    public function addDefaultAlert(\SafeStartApi\Entity\DefaultAlert $alerts)
    {
        $this->default_alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \SafeStartApi\Entity\Alert $alerts
     */
    public function removeDefaultAlert(\SafeStartApi\Entity\Alert $alerts)
    {
        $this->default_alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDefaultAlerts()
    {
        $alerts = array();
        if (!$this->default_alerts) return $alerts;
        foreach ($this->default_alerts as $alert) {
            if (!$alert->getDeleted()) {
                $alerts[] = $alert;
            }
        }
        return $alerts;
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getAlertsArray($filters = array())
    {
        $alerts = array();
        if ($checkListAlert = $this->getAlerts()) {
            foreach ($checkListAlert as $alert) {
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

        if ($defCheckListAlert = $this->getDefaultAlerts()) {
            foreach ($defCheckListAlert as $alert) {
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

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'hash' => $this->getHash(),
            'gps' => $this->getGpsCoords(),
            'location' => $this->getLocation(),
            'operator_name' => $this->getOperatorName(),
            'odometer_kms' => $this->getCurrentOdometer(),
            'odometer_hours' => $this->getCurrentOdometerHours(),
            'creation_date' => $this->getCreationDate()->getTimestamp(),
            'update_date' => $this->getUpdateDate()->getTimestamp(),
            'vehicle' => $this->getVehicle()->toInfoArray(),
            'data' => json_decode($this->getFieldsData(), true),
            'warnings' => $this->getWarnings()
        );
    }

    /**
     * @param $field
     * @return null
     */
    public function getFieldValue($field)
    {
        $value = null;
        $fieldsValue = json_decode($this->getFieldsData(), true);
        if(is_array($fieldsValue)) {
            foreach ($fieldsValue as $fieldValue) {
                if ($fieldValue['id'] == $field->getId()) {
                    $value = $fieldValue['value'];
                    break;
                }
            }
        }
        return $value;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return User
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
     * Set warnings
     *
     * @param array $warnings
     * @return CheckList
     */
    public function setWarnings($warnings)
    {
        $currentWarnings = $this->getWarnings();
        $currentWarnings = array_merge($currentWarnings, $warnings);
        $this->warnings = json_encode($currentWarnings);
        return $this;
    }

    public function clearWarnings() {
        $this->warnings = null;
        return $this;
    }

    /**
     * Get warnings
     *
     * @return array
     */
    public function getWarnings()
    {
        return $this->warnings ? json_decode($this->warnings, true) : array();
    }

    /**
     * @param string $warning
     */
    public function addWarning($warning = '')
    {
        $this->setWarnings(array(array(
            'date' => time(),
            'user' => \SafeStartApi\Application::getCurrentUser()->toInfoArray(),
            'action' => $warning
        )));
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
     * @return CheckList
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
     * Get location
     *
     * @param $location
     * @return string
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }


    public function getEmailMode()
    {
        return $this->email_mode;
    }

    public function setEmailMode($val)
    {
        $this->email_mode = $val;
        return $this;
    }


}