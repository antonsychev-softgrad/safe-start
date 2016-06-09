<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\CommentedEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use SafeStartApi\Entity\Vehicle;
use SafeStartApi\Entity\FaultReport;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="inspection_alerts")
 *
 */
class Alert extends BaseEntity
{
    // statuses
    const STATUS_NEW = 'new';
    const STATUS_CLOSED = 'closed';

    //actions
    const ACTION_STATUS_CHANGED_CLOSED = 'alert_closed';
    const ACTION_STATUS_CHANGED_NEW = 'alert_reopened';
    const ACTION_REFRESHED = 'alert_refreshed';
    const ACTION_FAULT_RECTIFICATION_EXTEND = 'alert_fault_extend';
    const ACTION_FAULT_RECTIFICATION_MONITOR = 'alert_fault_monitor';

    //expiry date description and etc
    const EXPIRY_DATE = 'Vehicle registration has expired';
    const DUE_SERVICE = 'Due For Service';
    const INACCURATE_KM_HR = 'Inaccurate Current Hours Or Kms';

    //mail statuses
    const MAIL_STATUS_SENT_INITIAL = 10;
    const MAIL_STATUS_SENT_7DAYS = 20;
    const MAIL_STATUS_SENT_24HOURS = 30;
    const MAIL_STATUS_SENT_OVERDUE = 40;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::STATUS_NEW;
        $this->serviceReports = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var string
     */
    protected $comment_entity = 'alert';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="CheckList", inversedBy="alerts")
     * @ORM\JoinColumn(name="check_list_id", referencedColumnName="id")
     **/
    protected $check_list;

    /**
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="alerts")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id")
     **/
    protected $field;

    /**
     * @ORM\ManyToOne(targetEntity="Vehicle", inversedBy="alerts")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     **/
    protected $vehicle;

    /**
     * @ORM\OneToMany(targetEntity="ServiceReport", mappedBy="alert", cascade={"persist", "remove", "merge"})
     */
    protected $serviceReports;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     *
     * PHP array using json_encode() and json_decode()
     */
    protected $images = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

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
     * @ORM\ManyToOne(targetEntity="FaultReport")
     * @ORM\JoinColumn(name="fault_report_id", referencedColumnName="id")
     **/
    protected $faultReport;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     *
     * PHP array using json_encode() and json_decode()
     */
    protected $history = '';

    /**
     * @ORM\Column(type="datetime", name="due_date")
     */
    protected $due_date;

    /**
     * @ORM\Column(type="boolean", name="monitor")
     */
    protected $monitor = 0;

    /**
     * @ORM\Column(type="integer", name="mail_status")
     */
    protected $mail_status = 0;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->creation_date) $this->setCreationDate(new \DateTime());
        $this->setUpdateDate(new \DateTime());

        if (!$this->due_date)
        {
            $field = $this->getField();
            if ($field)
            {
                $faultRectification = $field->getFaultRectification();
	            $due_date = clone $this->getCreationDate();
                $due_date->add(new \DateInterval("P{$faultRectification}D"));
                $this->setCreationDate($due_date);
            }
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
     * Add service report
     *
     * @param \SafeStartApi\Entity\ServiceReport $alerts
     * @return Alert
     */
    public function addServiceReport(\SafeStartApi\Entity\ServiceReport $serviceReports)
    {
        $this->serviceReports[] = $serviceReports;

        return $this;
    }

    /**
     * Get service reports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServiceReports()
    {
        $serviceReports = array();
        if (!$this->serviceReports) return $serviceReports;
        foreach ($this->serviceReports as $serviceReport) {
            if (!$serviceReport->getDeleted()) {
                $serviceReports[] = $serviceReport;
            }
        }
        return $serviceReports;
    }

    /**
     * Set comment
     *
     * @param $description
     * @return Alert
     */
    public function setDescription($description)
    {
        $this->description = strip_tags($description);

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set images
     *
     * @param array $images
     * @return Alert
     */
    public function setImages($images)
    {
        $this->images = json_encode($images);

        return $this;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        return $this->images ? json_decode($this->images, true) : array();
    }

    /**
     * Set history
     *
     * @param array $history
     * @return Alert
     */
    public function setHistory($history)
    {
        $currentHistory = $this->getHistory();
        $currentHistory = array_merge($currentHistory, $history);
        $this->history = json_encode($currentHistory);
        return $this;
    }

    public function addHistoryItem($action = '', $ext = null)
    {
        $data = array(
           'date' => time(),
           'user' => \SafeStartApi\Application::getCurrentUser()->toInfoArray(),
           'action' => $action
        );
        if ($ext != null)
        {
            $data = array_merge($data, $ext);
        }
        $this->setHistory(array($data));
    }

    public function getRefreshedTimes()
    {
        $count = 0;
        $currentHistory = $this->getHistory();
        foreach($currentHistory as $historyItem) {
            if (isset($historyItem['action']) && $historyItem['action'] == self::ACTION_REFRESHED) $count++;
        }
        return $count;
    }

    /**
     * Get history
     *
     * @return array
     */
    public function getHistory()
    {
        return $this->history ? json_decode($this->history, true) : array();
    }

    /**
     * Set check_list
     *
     * @param \SafeStartApi\Entity\CheckList $checkList
     * @return Alert
     */
    public function setCheckList(\SafeStartApi\Entity\CheckList $checkList = null)
    {
        $this->check_list = $checkList;

        return $this;
    }

    /**
     * Get check_list
     *
     * @return \SafeStartApi\Entity\CheckList
     */
    public function getCheckList()
    {
        return $this->check_list;
    }

    /**
     * Set field
     *
     * @param \SafeStartApi\Entity\Field $field
     * @return Alert
     */
    public function setField(\SafeStartApi\Entity\Field $field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return \SafeStartApi\Entity\Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set vehicle
     *
     * @param \SafeStartApi\Entity\Vehicle $vehicle
     * @return Alert
     */
    public function setVehicle(Vehicle $vehicle = null)
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
     * Set field
     *
     * @param $status
     * @throws \InvalidArgumentException
     * @return Alert
     */
    public function setStatus($status)
    {
        if (!in_array($status, array(self::STATUS_NEW, self::STATUS_CLOSED))) {
            throw new \InvalidArgumentException("Invalid alert status");
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Get field
     *
     * @return \SafeStartApi\Entity\Field
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {

        $title = $this->getDescription()
            ? $this->getDescription()
            : ($this->field
                ? ($this->field->getAlertDescription()
                    ? $this->field->getAlertDescription() : $this->field->getAlertTitle())
                : '');

        $data = array(
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'title' => $title,
            'alert_description' => $title,
            'field' => $this->field ? $this->field->toArray() : ($this->getDescription() ? array('alert_critical'=>1) : null),
            'vehicle' => $this->getVehicle()->toInfoArray(),
            'user' => $this->check_list ? $this->check_list->getUser()->toInfoArray() : ($this->getFaultReport() ? $this->getFaultReport()->getUser()->toInfoArray() : (object) array()),
            'fault_report' => $this->getFaultReport() ? $this->getFaultReport()->toArray() : (object) array(),
            'description' => $this->getDescription(),
            'images' => $this->getImages(),
            'thumbnail' => $this->getThumbnail(),
            'comments' => $this->getComments(),
            'creation_date' => $this->getCreationDate()->getTimestamp(),
            'update_date' => $this->getUpdateDate()->getTimestamp(),
            'history' => $this->getHistory(),
            'due_date' => $this->getDueDate()->getTimestamp(),
            'monitor' => (int)$this->monitor,
            'mail_status' => $this->getMailStatus(),
            'refreshed_times' => $this->getRefreshedTimes(),
            'service_reports' => array()
        );

        $reports = $this->getServiceReports();
        if ($reports && sizeof($reports) > 0)
        {
            foreach ($reports as $report)
            {
                $data['service_reports'][] = $report->toArray();
            }
        }

        return $data;
    }

    public function toExportArray()
    {
//        $config = \SafeStartApi\Application::getConfig();
//        $comments = $this->getComments();
//        $comments = array_map(function($item) {
//            return $item['update_date'] . ": " . preg_replace("/\s+/s", ' ', $item['content']);
//        }, $comments);

        $data = array(
            'title' => $this->getDescription() ? $this->getDescription():($this->field ?($this->field->getAlertDescription() ? $this->field->getAlertDescription() : $this->field->getAlertTitle()): ''),
//            'status' => $this->getStatus(),
//            'creation_date' => date($config['params']['date_format'], $this->getCreationDate()->getTimestamp()),
//            'update_date' => date($config['params']['date_format'], $this->getUpdateDate()->getTimestamp()),
//            'comments' => implode("\r\n", $comments),
        );
        return $data;
    }

    public function getThumbnail()
    {
        $src = '';
        if (!empty($this->images) && isset($this->getImages()[0])) $src = '/api/image/' . $this->getImages()[0] . '/' . \SafeStartApi\Controller\Plugin\UploadPlugin::THUMBNAIL_SMALL;
        return $src;
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
     * Set deleted
     *
     * @param boolean $deleted
     * @return Alert
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
     * Get faultReport
     *
     * @return \SafeStartApi\Entity\FaultReport
     */
    public function getFaultReport()
    {
        return $this->faultReport;
    }

    /**
     * Set faultReport
     *
     * @param \SafeStartApi\Entity\FaultReport $faultReport
     * @return Alert
     */
    public function setFaultReport(\SafeStartApi\Entity\FaultReport $faultReport = null)
    {
        $this->faultReport = $faultReport;

        return $this;
    }

    /**
     * Set creation_date
     *
     * @param \DateTime $creationDate
     * @return Alert
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
     * Set due_date
     *
     * @param \DateTime $date
     * @return CheckList
     */
    public function setDueDate($date)
    {
        $this->due_date = $date;

        return $this;
    }

    /**
     * Set defaults
     *
     * @return Alert
     */
    public function setDefaultsForRenew()
    {
        $field = $this->getField();
        $creation_date = $this->creation_date ? $this->creation_date : new \DateTime();
        if ($field)
        {
            $faultRectification = $field->getFaultRectification();
            $due_date = clone $creation_date;
            $due_date->add(new \DateInterval("P{$faultRectification}D"));
        }
        else
        {
            $due_date = clone $creation_date;
        }

        $this->due_date = $due_date;
        $this->setMonitor(0);
        $this->setMailStatus(0);

        return $this;
    }

    /**
     * Get due_date
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
		$due_date = $this->due_date;
        if (!$due_date)
        {
            $field = $this->getField();
            if ($field)
            {
                $faultRectification = $field->getFaultRectification();
	            $due_date = clone $this->getCreationDate();
                $due_date->add(new \DateInterval("P{$faultRectification}D"));
            }
			else
            {
	            $due_date = clone $this->getCreationDate();
            }
        }

        return $due_date;
    }

    /**
     * Set monitor
     *
     * @param boolean $monitor
     * @return CheckList
     */
    public function setMonitor($monitor)
    {
        $this->monitor = $monitor;

        return $this;
    }

    /**
     * Get monitor
     *
     * @return boolean
     */
    public function getMonitor()
    {
        return $this->monitor;
    }

    /**
     * Set mail_status
     *
     * @param integer $mail_status
     * @return CheckList
     */
    public function setMailStatus($mail_status)
    {
        $this->mail_status = $mail_status;

        return $this;
    }

    /**
     * Get mail_status
     *
     * @return integer
     */
    public function getMailStatus()
    {
        return $this->mail_status;
    }
}
