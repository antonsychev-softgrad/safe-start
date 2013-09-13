<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\CommentedEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="inspection_alerts")
 *
 */
class Alert extends BaseEntity
{
    const STATUS_NEW = 'new';
    const STATUS_CLOSED = 'closed';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::STATUS_NEW;
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     *
     * PHP array using json_encode() and json_decode()
     */
    protected $images;

    /**
     * Magic getter to expose protected properties.
     *
     * @param string $property
     * @return mixed
     */

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", name="creation_date")
     */
    protected $creation_date;

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
     * Set comment
     *
     * @param $description
     * @return Alert
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
        return json_decode($this->images);
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
        $data = array(
            'id' => $this->getId(),
            'status' => $this->getStatus(),
            'title' => $this->check_list->getCreationDate()->format('Y-m-d H:i'),
            'alert_description' => $this->field ? $this->field->getAlertDescription() : '',
            'vehicle' => $this->getVehicle()->toInfoArray(),
            'user' => $this->check_list->getUser()->toInfoArray(),
            'description' => $this->getDescription(),
            'images' => $this->getImages(),
            'thumbnail' => $this->getThumbnail(),
            'comments' => $this->getComments(),
            'creation_date' => $this->getCreationDate()
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
}