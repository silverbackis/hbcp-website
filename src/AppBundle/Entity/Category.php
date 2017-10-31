<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"cat_select"})
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"cat_select"})
     */
    private $name;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Category", inversedBy="children")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    public $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Category", mappedBy="parent", fetch="EAGER")
     */
    public $children;

    /**
     * @var int
     * @ORM\Column(name="fixed", type="boolean")
     * @Groups({"cat_select"})
     */
    private $fixed = false;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Resource", mappedBy="category")
     */
    private $resources;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
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
     * Set fixed
     *
     * @param boolean $fixed
     *
     * @return Category
     */
    public function setFixed($fixed)
    {
        $this->fixed = $fixed;

        return $this;
    }

    /**
     * Get fixed
     *
     * @return boolean
     */
    public function getFixed()
    {
        return $this->fixed;
    }

    /**
     * Set parent
     *
     * @param null|Category $parent
     *
     * @return Category
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return null|Category
     */
    public function getParent()
    {
        return $this->parent;
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Category
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
     * @return Category
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(\AppBundle\Entity\Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Category $child
     */
    public function removeChild(\AppBundle\Entity\Category $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getBreadcrumbs($includeSelf = null)
    {
        $names = [];
        if ($includeSelf === true || (is_null($includeSelf) && $this->getFixed())) {
            $names[] = $this->getName();
        }
        $parent = $this->getParent();
        while($parent) {
            $names[] = $parent->getName();
            $parent = $parent->getParent();
        }
        $names = array_reverse($names);
        return join (" > ", $names);
    }

    /**
     * Add resource
     *
     * @param \AppBundle\Entity\Resource $resource
     *
     * @return Category
     */
    public function addResource(\AppBundle\Entity\Resource $resource)
    {
        $this->resources[] = $resource;

        return $this;
    }

    /**
     * Remove resource
     *
     * @param \AppBundle\Entity\Resource $resource
     */
    public function removeResource(\AppBundle\Entity\Resource $resource)
    {
        $this->resources->removeElement($resource);
    }

    /**
     * Get resources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResources()
    {
        return $this->resources;
    }
}
