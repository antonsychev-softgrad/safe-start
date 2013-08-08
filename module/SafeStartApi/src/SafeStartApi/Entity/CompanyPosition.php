<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Companies
 * @ORM\Entity
 * @ORM\Table(name="company_positions")
 */

class CompanyPosition extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Company", inversedBy="positions")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     **/
    protected $company;

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
     * @return Position
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
     * Set company
     *
     * @param \SafeStartApi\Entity\Company $company
     * @return CompanyPosition
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
}