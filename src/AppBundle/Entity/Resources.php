<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Category;
/**
 * Resources
 *
 * @ORM\Table(name="resources")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourcesRepository")
 */
class Resources
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="path_type", type="string", length=255)
     */
    private $pathType;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="text")
     */
    private $path;

    /**
     * @var int
     *
     * @ORM\Column(name="category", type="bigint")
     */
    public $category;

    /**
     * @var int
     *
     * @ORM\Column(name="categoryid", type="integer")
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="resources")
     * @ORM\JoinColumn(name="categoryid", referencedColumnName="id")
     */
    public $categoryid;



    /**
     * @var int
     *
     * @ORM\Column(name="resource_type", type="bigint")
     */
    public $resourceType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="added_datetime", type="datetime")
     */
    private $addedDatetime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified_datetime", type="datetime")
     */
    private $modifiedDatetime;
    
    /**
     * @var string
     *
     * @ORM\Column(name="resource_information", type="string", length=255 ,nullable=true)
     */
    private $resourceInformation;

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
     * Set title
     *
     * @param string $title
     *
     * @return Resources
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
     * Set pathType
     *
     * @param string $pathType
     *
     * @return Resources
     */
    public function setPathType($pathType)
    {
        $this->pathType = $pathType;

        return $this;
    }

    /**
     * Get pathType
     *
     * @return string
     */
    public function getPathType()
    {
        return $this->pathType;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Resources
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set category
     *
     * @param integer $category
     *
     * @return Resources
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set resourceType
     *
     * @param integer $resourceType
     *
     * @return Resources
     */
    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    /**
     * Get resourceType
     *
     * @return int
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Set addedDatetime
     *
     * @param \DateTime $addedDatetime
     *
     * @return Resources
     */
    public function setAddedDatetime($addedDatetime)
    {
        $this->addedDatetime = $addedDatetime;

        return $this;
    }

    /**
     * Get addedDatetime
     *
     * @return \DateTime
     */
    public function getAddedDatetime()
    {
        return $this->addedDatetime;
    }

    /**
     * Set modifiedDatetime
     *
     * @param \DateTime $modifiedDatetime
     *
     * @return Resources
     */
    public function setModifiedDatetime($modifiedDatetime)
    {
        $this->modifiedDatetime = $modifiedDatetime;

        return $this;
    }

    /**
     * Get modifiedDatetime
     *
     * @return \DateTime
     */
    public function getModifiedDatetime()
    {
        return $this->modifiedDatetime;
    }

    /**
     * Set categoryid
     *
     * @param integer $categoryid
     *
     * @return Resources
     */
    public function setCategoryid($categoryid)
    {
        $this->categoryid = $categoryid;

        return $this;
    }

    /**
     * Get categoryid
     *
     * @return int
     */
    public function getCategoryid()
    {
        return $this->categoryid;
    }

    

     /**
     * Set resourceInformation
     *
     * @param string $resourceInformation
     *
     * @return Resources
     */
    public function setResourceInformation($resourceInformation)
    {
        $this->resourceInformation = $resourceInformation;

        return $this;
    }

    /**
     * Get resourceInformation
     *
     * @return string
     */
    public function getResourceInformation()
    {
        return $this->resourceInformation;
    }

   
}

