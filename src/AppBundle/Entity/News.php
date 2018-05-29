<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * News
 *
 * @ORM\Table(name="news")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NewsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class News
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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=true)
     */
    private $author;

    /**
    * @var string
    *
    * @ORM\Column(name="image_path", type="text", nullable=true)
    * @Assert\File(mimeTypes={ "image/jpeg", "image/png" }, maxSize="4Mi")
    */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_credit", type="text", nullable=true)
     */
    private $imageCredit;


    /**
     * @var string
     *
     * @ORM\Column(name="news_content", type="text", nullable=true)
     */
    private $newsContent;

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
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getImage()) {
            if (!$this->getImageCredit()) {
                $context->buildViolation('An image credit is required when you upload an image')
                    ->atPath('imageCredit')
                    ->addViolation();
            }
        }
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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return News
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
     * Set author
     *
     * @param string $author
     *
     * @return News
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Get added
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set image
     *
     * @param null|string $image
     *
     * @return News
     */
    public function setImage(File $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return null|File
     */
    public function getImage()
    {
        try {
            return $this->image ? new File($this->image) : null;
        } catch (FileNotFoundException $e) {
            return null;
        }
    }

    public function getImagePath()
    {
        return $this->image && strpos($this->image, '/web') ? explode('/web', $this->image)[1] : null;
    }

    /**
     * Set imageCredit
     *
     * @param string $imageCredit
     *
     * @return News
     */
    public function setImageCredit($imageCredit)
    {
        $this->imageCredit = $imageCredit;

        return $this;
    }

    /**
     * Get imageCredit
     *
     * @return string
     */
    public function getImageCredit()
    {
        return $this->imageCredit;
    }


    /**
     * Set newsContent
     *
     * @param string $newsContent
     *
     * @return News
     */
    public function setNewsContent($newsContent)
    {
        $this->newsContent = $newsContent;

        return $this;
    }

    /**
     * Get newsContent
     *
     * @return string
     */
    public function getNewsContent()
    {
        return $this->newsContent;
    }
}
