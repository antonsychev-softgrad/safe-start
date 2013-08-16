<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Zend\Crypt\Password\Bcrypt;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;


// @UniqueEntity("email")
// @UniqueEntity("username")

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 */
class User extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $username;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $role = 'guest';

    /**
     * @ORM\Column(type="string", name="first_name", nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", name="last_name", nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", name="second_name", nullable=true)
     */
    protected $secondName;

    /**
     * @ORM\OneToOne(targetEntity="Company", inversedBy="users")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     **/
    protected $company;

    /**
     * @ORM\OneToMany(targetEntity="Vehicle", mappedBy="responsibleUser")
    */
    protected $vehiclesAsigned;

    /**
     * @ORM\ManyToMany(targetEntity="Vehicle", mappedBy="endUsers")
    */
    protected $vehicles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $locale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $timezone;

    /**
     * @ORM\Column(type="string", name="position", nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="string", name="department", nullable=true)
     */
    protected $department;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true, unique=true, name="last_login")
     */
    protected $lastLogin;

    public function toArray()
    {
        return array(
          'id'          => $this->getId(),
          'email'       => (!is_null($this->email)) ? $this->email : '',
          'username'    => (!is_null($this->username)) ? $this->username : '',
          'firstName'   => (!is_null($this->firstName)) ? $this->firstName : '',
          'lastName'    => (!is_null($this->lastName)) ? $this->lastName : '',
          'secondName'  => (!is_null($this->secondName)) ? $this->secondName : '',
          'role'        => $this->getRole(),
          'companyId'   => (!is_null($this->company)) ? $this->getCompany()->getId() : 0,
          'position'   => (!is_null($this->position)) ? $this->position: '',
          'department'   => (!is_null($this->company)) ? $this->department: '',
        );
    }

    public static function hashPassword($password)
    {
        $bcrypt = new Bcrypt();

        return $bcrypt->create($password);
    }

    public static function verifyPassword(User $user, $password)
    {
        $bcrypt = new Bcrypt();

        return $bcrypt->verify($password, $user->getPassword());
    }

    public function setPlainPassword($plainPassword)
    {
        $this->password = self::hashPassword($plainPassword);

        return $this;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enabled = false;
        $this->vehiclesAsigned = new \Doctrine\Common\Collections\ArrayCollection();
        $this->vehicles = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return User
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set secondName
     *
     * @param string $secondName
     * @return User
     */
    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;

        return $this;
    }

    /**
     * Get secondName
     *
     * @return string
     */
    public function getSecondName()
    {
        return $this->secondName;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return User
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
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set company
     *
     * @param \SafeStartApi\Entity\Company $company
     * @return User
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
     * Add vehiclesAsigned
     *
     * @param \SafeStartApi\Entity\Vehicle $vehiclesAsigned
     * @return User
     */
    public function addVehiclesAsigned(\SafeStartApi\Entity\Vehicle $vehiclesAsigned)
    {
        $this->vehiclesAsigned[] = $vehiclesAsigned;

        return $this;
    }

    /**
     * Remove vehiclesAsigned
     *
     * @param \SafeStartApi\Entity\Vehicle $vehiclesAsigned
     */
    public function removeVehiclesAsigned(\SafeStartApi\Entity\Vehicle $vehiclesAsigned)
    {
        $this->vehiclesAsigned->removeElement($vehiclesAsigned);
    }

    /**
     * Get vehiclesAsigned
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVehiclesAsigned()
    {
        return $this->vehiclesAsigned;
    }

    /**
     * Add vehicles
     *
     * @param \SafeStartApi\Entity\Vehicle $vehicles
     * @return User
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
     * Set position
     *
     * @param string $position
     * @return User
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return string 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return User
     */
    public function setDepartment($department)
    {
        $this->department = $department;
    
        return $this;
    }

    /**
     * Get department
     *
     * @return string 
     */
    public function getDepartment()
    {
        return $this->department;
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
}