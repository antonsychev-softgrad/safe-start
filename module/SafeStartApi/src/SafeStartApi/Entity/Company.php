<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * @ORM\Entity
 * @ORM\Table(name="companies")
 *
 */
class Company extends BaseEntity
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responsiblePersons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vehicles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->restricted = false;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $admin;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="companies_responsible_persons",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $responsiblePersons;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     **/
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="company")
     **/
    protected $vehicles;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;


    /**
     * @ORM\Column(type="text", name="description", nullable = true)
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean", name="restricted", nullable=true)
     */
    protected $restricted;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="expiry_date")
     */
    protected $expiryDate;

    /**
     * @ORM\Column(type="integer", nullable=true, name="max_users")
     */
    protected $maxUsers;

    /**
     * @ORM\Column(type="integer", nullable=true, name="max_vehicles")
     */
    protected $maxVehicles;

    /**
     * @ORM\Column(type="boolean", name="deleted", nullable=true)
     */
    protected $deleted;

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
     * @return Company
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
     * Set address
     *
     * @param string $address
     * @return Company
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Company
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Company
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }


    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }


    /**
     * Add admins
     *
     * @param \SafeStartApi\Entity\User $admins
     * @return Company
     */
    public function addAdmin(\SafeStartApi\Entity\User $admins)
    {
        $this->admins[] = $admins;
    
        return $this;
    }

    /**
     * Remove admins
     *
     * @param \SafeStartApi\Entity\User $admins
     */
    public function removeAdmin(\SafeStartApi\Entity\User $admins)
    {
        $this->admins->removeElement($admins);
    }

    /**
     * Get admins
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * Add responsiblePersons
     *
     * @param \SafeStartApi\Entity\User $responsiblePersons
     * @return Company
     */
    public function addResponsiblePerson(\SafeStartApi\Entity\User $responsiblePersons)
    {
        $this->responsiblePersons[] = $responsiblePersons;
    
        return $this;
    }

    /**
     * Remove responsiblePersons
     *
     * @param \SafeStartApi\Entity\User $responsiblePersons
     */
    public function removeResponsiblePerson(\SafeStartApi\Entity\User $responsiblePersons)
    {
        $this->responsiblePersons->removeElement($responsiblePersons);
    }

    /**
     * Get responsiblePersons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResponsiblePersons()
    {
        return $this->responsiblePersons;
    }

    /**
     * Add users
     *
     * @param \SafeStartApi\Entity\User $users
     * @return Company
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
     * Add vehicles
     *
     * @param \SafeStartApi\Entity\Vehicle $vehicles
     * @return Company
     */
    public function addVehicle(\SafeStartApi\Entity\Vehicle $vehicles)
    {
        $this->vehicles[] = $vehicles;
    
        return $this;
    }

    /**
     * Remove vehicles
     *
     * @param \SafeStartApi\Entity\Vehicle $vehicles
     */
    public function removeVehicle(\SafeStartApi\Entity\Vehicle $vehicles)
    {
        $this->vehicles->removeElement($vehicles);
    }

    /**
     * Get vehicles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVehicles()
    {
        return $this->vehicles;
    }

    /**
     * Set restricted
     *
     * @param boolean $restricted
     * @return Company
     */
    public function setRestricted($restricted)
    {
        $this->restricted = $restricted;
    
        return $this;
    }

    /**
     * Get restricted
     *
     * @return boolean 
     */
    public function getRestricted()
    {
        return $this->restricted;
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
     * Get expiryDate
     *
     * @return \DateTime 
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set maxUsers
     *
     * @param integer $maxUsers
     * @return Company
     */
    public function setMaxUsers($maxUsers)
    {
        $this->maxUsers = $maxUsers;
    
        return $this;
    }

    /**
     * Get maxUsers
     *
     * @return integer 
     */
    public function getMaxUsers()
    {
        return $this->maxUsers;
    }

    /**
     * Set maxVehicles
     *
     * @param integer $maxVehicles
     * @return Company
     */
    public function setMaxVehicles($maxVehicles)
    {
        $this->maxVehicles = $maxVehicles;
    
        return $this;
    }

    /**
     * Get maxVehicles
     *
     * @return integer 
     */
    public function getMaxVehicles()
    {
        return $this->maxVehicles;
    }
}