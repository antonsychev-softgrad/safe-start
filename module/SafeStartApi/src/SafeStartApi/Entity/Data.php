<?php

namespace SafeStartApi\Entity;

use SafeStartApi\Base\Entity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;



/**
 * @ORM\Entity
 * @ORM\Table(name="data")
 */
class Data extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $key;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $value;



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
     * Set entity
     *
     * @param string $entity
     * @return Data
     */
    public function setKey($entity)
    {
        $this->key = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return Data
     */
    public function setValue($entity)
    {
        $this->value = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @return array
     */
    public function toArray() {
        return array(
            'id' => $this->id,
            'key' => $this->getKey(),
            'value' => $this->getValue()
        );
    }
}
