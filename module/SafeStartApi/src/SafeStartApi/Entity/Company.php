<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;


/**
 * @ORM\Entity
 * @ORM\Table(name="companies")
 *
 */
class Company extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Department", mappedBy="company")
     **/
    protected $departments;

    /**
     * @ORM\OneToMany(targetEntity="CompanyPosition", mappedBy="company")
     **/
    protected $positions;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="companies_admins",
     *      joinColumns={@JoinColumn(name="company_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id", unique=true)}
     *      )
     */
    protected $admins;

    /**
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinTable(name="companies_responsible_persons",
     *      joinColumns={@JoinColumn(name="company_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id", unique=true)}
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
     * Constructor
     */
    public function __construct()
    {
        $this->departments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->positions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->admins = new \Doctrine\Common\Collections\ArrayCollection();
        $this->responsiblePersons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add departments
     *
     * @param \SafeStartApi\Entity\Department $departments
     * @return Company
     */
    public function addDepartment(\SafeStartApi\Entity\Department $departments)
    {
        $this->departments[] = $departments;

        return $this;
    }

    /**
     * Remove departments
     *
     * @param \SafeStartApi\Entity\Department $departments
     */
    public function removeDepartment(\SafeStartApi\Entity\Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    /**
     * Get departments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDepartments()
    {
        return $this->departments;
    }

    /**
     * Add positions
     *
     * @param \SafeStartApi\Entity\CompanyPosition $positions
     * @return Company
     */
    public function addPosition(\SafeStartApi\Entity\CompanyPosition $positions)
    {
        $this->positions[] = $positions;

        return $this;
    }

    /**
     * Remove positions
     *
     * @param \SafeStartApi\Entity\CompanyPosition $positions
     */
    public function removePosition(\SafeStartApi\Entity\CompanyPosition $positions)
    {
        $this->positions->removeElement($positions);
    }

    /**
     * Get positions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPositions()
    {
        return $this->positions;
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
}