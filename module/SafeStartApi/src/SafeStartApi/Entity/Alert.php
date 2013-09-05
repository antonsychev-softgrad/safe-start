<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="inspection_alerts")
 *
 */
class Alert extends BaseEntity
{
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

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
     * Set title
     *
     * @param string $title
     * @return Alert
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Alert
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set images
     *
     * @param array $images
     * @return Alert
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        return $this->images;
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
}