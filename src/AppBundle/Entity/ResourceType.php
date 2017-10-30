<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceType
 *
 * @ORM\Table(name="resource_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourceTypeRepository")
 */
class ResourceType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdtime", type="datetime")
     */
    private $createdtime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedtime", type="datetime")
     */
    private $updatedtime;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ResourceType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdtime
     *
     * @param \DateTime $createdtime
     *
     * @return ResourceType
     */
    public function setCreatedtime($createdtime)
    {
        $this->createdtime = $createdtime;

        return $this;
    }

    /**
     * Get createdtime
     *
     * @return \DateTime
     */
    public function getCreatedtime()
    {
        return $this->createdtime;
    }

    /**
     * Set updatedtime
     *
     * @param \DateTime $updatedtime
     *
     * @return ResourceType
     */
    public function setUpdatedtime($updatedtime)
    {
        $this->updatedtime = $updatedtime;

        return $this;
    }

    /**
     * Get updatedtime
     *
     * @return \DateTime
     */
    public function getUpdatedtime()
    {
        return $this->updatedtime;
    }
}

