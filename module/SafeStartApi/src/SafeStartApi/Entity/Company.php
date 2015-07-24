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
        $this->responsibleUsers = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->vehicles = new ArrayCollection();
        $this->restricted = false;
        $this->unlim_expiry_date = false;
        $this->unlim_users = false;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $admin;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="companies_responsible_users",
     *      joinColumns={@ORM\JoinColumn(name="company_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     *      )
     */
    protected $responsibleUsers;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     **/
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="company")
     **/
    protected $vehicles;

    /**
     * @ORM\Column(type="string")
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
    protected $deleted = 0;

    /**
     * @ORM\Column(type="text", name="logo", nullable = true)
     */
    protected $logo;

    /**
     * @ORM\Column(type="boolean", name="unlim_expiry_date", nullable=true)
     */
    protected $unlim_expiry_date;

    /**
     * @ORM\Column(type="boolean", name="unlim_users", nullable=true)
     */
    protected $unlim_users;

    /**
     * @ORM\Column(type="string", name="payment_type", nullable=true)
     */
    protected $payment_type = "Free";

    /**
     * @ORM\Column(type="datetime", name="payment_date", nullable=true)
     */
    protected $payment_date;

    /**
     * @ORM\Column(type="json_array", name="other_users", nullable=true)
     */
    protected $otherUsers = [];

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
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set logo
     *
     * @param $val
     * @return Company
     */
    public function setLogo($val)
    {
        $this->logo = $val;

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
     * Add responsibleUsers
     *
     * @param \SafeStartApi\Entity\User $responsibleUsers
     * @return Company
     */
    public function addResponsibleUser(\SafeStartApi\Entity\User $responsibleUsers)
    {
        $this->responsibleUsers[] = $responsibleUsers;
    
        return $this;
    }

    /**
     * Remove responsibleUsers
     *
     * @param \SafeStartApi\Entity\User $responsibleUsers
     */
    public function removeResponsibleUser(\SafeStartApi\Entity\User $responsibleUsers)
    {
        $this->responsibleUsers->removeElement($responsibleUsers);
    }

    /**
     * Get responsibleUsers
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
        $users = array();
        if (!$this->users) return $users;
        foreach ($this->users as $user) {
            if (!$user->getDeleted()) {
                $users[] = $user;
            }
        }
        return $users;
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
        $vehicles = array();
        if (!$this->vehicles) return $vehicles;
        foreach ($this->vehicles as $vehicle) {
            if (!$vehicle->getDeleted()) {
                $vehicles[] = $vehicle;
            }
        }
        return $vehicles;
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
        return $this->expiryDate ? $this->expiryDate->getTimestamp() : null;
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

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Company
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
     * Set admin
     *
     * @param \SafeStartApi\Entity\User $admin
     * @return Company
     */
    public function setAdmin(\SafeStartApi\Entity\User $admin = null)
    {
        $this->admin = $admin;
    
        return $this;
    }

    /**
     * Get admin
     *
     * @return \SafeStartApi\Entity\User 
     */
    public function getAdmin()
    {
        return $this->admin;
    }
    
    /**
     * Set otherUsers
     *
     * @param array $otherUsers
     * @return Company
     */
    public function setOtherUsers($otherUsers)
    {
        $this->otherUsers = $otherUsers;

        return $this;
    }

    /**
     * Get otherUsers
     *
     * @return array 
     */
    public function getOtherUsers()
    {
        return $this->otherUsers;
    }

    /**
     * Set unlim_expiry_date
     *
     * @param boolean $unlimExpiryDate
     * @return Company
     */
    public function setUnlimExpiryDate($unlimExpiryDate)
    {
        $this->unlim_expiry_date = $unlimExpiryDate;

        return $this;
    }

    /**
     * Get unlim_expiry_date
     *
     * @return boolean
     */
    public function getUnlimExpiryDate()
    {
        return $this->unlim_expiry_date;
    }

    /**
     * Set unlim_users
     *
     * @param boolean $unlimUsers
     * @return Company
     */
    public function setUnlimUsers($unlimUsers)
    {
        $this->unlim_users = $unlimUsers;

        return $this;
    }

    /**
     * Get unlim_users
     *
     * @return boolean
     */
    public function getUnlimUsers()
    {
        return $this->unlim_users;
    }

    /**
     * Set payment_type
     *
     * @param string $payment_type
     * @return Company
     */
    public function setPaymentType($payment_type)
    {
        $this->payment_type = $payment_type;

        return $this;
    }

    /**
     * Get payment_type
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * Set payment_date
     *
     * @param \DateTime $payment_date
     * @return Company
     */
    public function setPaymentDate($payment_date)
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    /**
     * Get payment_date
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->payment_date ? $this->payment_date->getTimestamp() : null;
    }

    /**
     * Convert the object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $company = array();
        $company['id'] = $this->getId();
        $company['title'] = $this->getTitle();
        $company['address'] = $this->getAddress();
        $company['restricted'] = $this->restricted;
        $company['unlim_expiry_date'] = $this->unlim_expiry_date;
        $company['unlim_users'] = $this->unlim_users;
        $company['phone'] = $this->phone;
        $company['description'] = $this->description;
        $company['logo'] = $this->logo;
        $company['max_users'] = $this->getMaxUsers();
        $company['max_vehicles'] = $this->getMaxVehicles();
        $company['expiry_date'] = $this->getExpiryDate();
        $company['email'] = $this->admin->email;
        $company['firstName'] = $this->admin->firstName;
        $company['other_users'] = $this->otherUsers;
        return $company;
    }

    public function haveAccess(User $user)
    {
        $companyAdmin = $this->getAdmin();
        if($user->getId() == $companyAdmin->getId()) {
            return true;
        }

        if($user->getRole() == 'superAdmin') {
            return true;
        }

        if($user->getRole() == 'companyManager' && $user->getCompany()->getId() == $this->getId()) {
            return true;
        }

        return false;
    }
}