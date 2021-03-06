<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resource
 * @ORM\Table(name="resource")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ResourcesRepository")
 * @ORM\HasLifecycleCallbacks()
 * @\AppBundle\Validator\Constraints\Resource
 */
class Resource
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
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     * @Assert\Choice({"OSF", "Dropbox", "Website", "Tool"})
     */
    private $pathType;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\Url()
     */
    private $path;

    /**
     * @var Category
     */
    public $topCategory;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="resources")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
     * @Assert\NotNull(message="Please select a category")
     */
    public $category;

    /**
     * @var ResourceType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ResourceType")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    public $resourceType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dropboxUploaded;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dropboxSize;

    /**
     * @param null|Category $topCategory
     * @ORM\PostLoad()
     * @return Resource
     */
    public function setTopCategory()
    {
        if (!$this->getCategory()) {
            $this->topCategory = null;
        } else {
            $parent = $this->getCategory();
            if (!$parent) {
                $parent = $this->getCategory();
            } else {
                while ($parent->getParent() && !$parent->getFixed()) {
                    $parent = $parent->getParent();
                }
            }
            $this->topCategory = $parent;
        }
        return $this;
    }

    public function getRootCategory()
    {
        $root = $this->getCategory();
        while ($root->getParent()) {
            $root = $root->getParent();
        }
        return $root;
    }

    // Return categories without top level parent
    public function getCategoryChain()
    {
        $cats = [];
        $root = $this->getCategory();
        if ($root === $this->getRootCategory()) {
            $cats[] = 'General';
        }
        while ($root->getParent()) {
            if ($root->getParent()) {
                $cats[] = $root->getName();
            }
            $root = $root->getParent();
        }
        $cats = array_reverse($cats);
        return $cats;
    }

    public function getResourceTag(): string
    {
        $category = $this->getCategory();
        if (strtolower($category->getName()) === 'project') {
            return 'Project';
        }
        $categoryCrumbs = $category->getBreadcrumbs(true, false);
        return $categoryCrumbs === '' ? 'General' : $categoryCrumbs;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updated = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getIdPadded()
    {
        return str_pad($this->id, 4, 0, STR_PAD_LEFT);
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Resource
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
     * @return Resource
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
     * @return Resource
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
     * @param Category $category
     *
     * @return Resource
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set resourceType
     *
     * @param null|ResourceType $resourceType
     *
     * @return Resource
     */
    public function setResourceType(ResourceType $resourceType = null)
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    /**
     * Get resourceType
     *
     * @return null|ResourceType
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return null|Category
     */
    public function getTopCategory()
    {
        return $this->topCategory;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Resource
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Resource
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Set dropboxUploaded
     *
     * @param \DateTime $dropboxUploaded
     *
     * @return Resource
     */
    public function setDropboxUploaded($dropboxUploaded)
    {
        $this->dropboxUploaded = $dropboxUploaded;

        return $this;
    }

    /**
     * Get dropboxUploaded
     *
     * @return \DateTime
     */
    public function getDropboxUploaded()
    {
        return $this->dropboxUploaded;
    }

    /**
     * Set dropboxSize
     *
     * @param integer $dropboxSize
     *
     * @return Resource
     */
    public function setDropboxSize($dropboxSize)
    {
        $this->dropboxSize = $dropboxSize;

        return $this;
    }

    /**
     * Get dropboxSize
     *
     * @return integer
     */
    public function getDropboxSize()
    {
        return $this->dropboxSize;
    }
}
