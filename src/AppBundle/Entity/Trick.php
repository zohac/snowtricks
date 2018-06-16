<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Service\Slugger\Slugger;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Trick.
 *
 * @ORM\Table(name="trick")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrickRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(fields="title", message="Le titre est déjà utilisé.")
 */
class Trick
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Regex(
     *      pattern="/^[a-zA-Z0-9\- ]+$/",
     *      message="le nom du trick ne doit comporter que des caractères alphanumérique"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateModified", type="datetime", nullable=true)
     */
    private $dateModified;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $modifiedBy;

    /**
     * @var Category
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categories;

    /**
     * @var Pictures
     *
     * @ORM\OneToMany(
     *      targetEntity="AppBundle\Entity\Picture",
     *      mappedBy="trick",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     * @Assert\Valid
     */
    private $pictures;

    /**
     * @var Video
     *
     * @ORM\OneToMany(
     *      targetEntity="AppBundle\Entity\Video",
     *      mappedBy="trick",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     * @Assert\Valid
     */
    private $videos;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->date = new \Datetime('NOW');
        $this->categories = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user.
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Trick
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Trick
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Trick
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Trick
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateModified.
     *
     * @param \DateTime|null $dateModified
     *
     * @return Trick
     */
    public function setDateModified($dateModified = null)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified.
     *
     * @return \DateTime|null
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set modifiedBy.
     *
     * @param \AppBundle\Entity\User|null $modifiedBy
     *
     * @return Trick
     */
    public function setModifiedBy(\AppBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy.
     *
     * @return \AppBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * @ORM\PreFlush
     */
    public function addSlug()
    {
        $slugger = new Slugger();
        $slug = $slugger->slugify($this->getTitle());
        $this->setSlug($slug);
    }

    /**
     * Get the value of date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date.
     *
     * @param \DateTime $date
     *
     * @return self
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Add category.
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Trick
     */
    public function addCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category.
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add picture.
     *
     * @param \AppBundle\Entity\Picture $picture
     *
     * @return Trick
     */
    public function addPicture(\AppBundle\Entity\Picture $picture)
    {
        // Bidirectional Ownership
        $picture->setTrick($this);

        $this->pictures[] = $picture;

        return $this;
    }

    /**
     * Remove picture.
     *
     * @param \AppBundle\Entity\Picture $picture
     */
    public function removePicture(\AppBundle\Entity\Picture $picture)
    {
        $this->pictures->removeElement($picture);
    }

    /**
     * Get pictures.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    /**
     * Retrieves the address of the highlighted image.
     *
     * @return string the path
     */
    public function getHeadLinePicturePath(): string
    {
        $path = Picture::DEFAULT_TRICK;
        foreach ($this->pictures as $picture) {
            if ($picture->isHeadLinePicture()) {
                return $picture->getPath();
            }
            $path = $picture->getPath();
        }

        return $path;
    }

    /**
     * Add video.
     *
     * @param \AppBundle\Entity\Video $video
     *
     * @return Trick
     */
    public function addVideo(\AppBundle\Entity\Video $video)
    {
        // Bidirectional Ownership
        $video->setTrick($this);

        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video.
     *
     * @param \AppBundle\Entity\Video $video
     */
    public function removeVideo(\AppBundle\Entity\Video $video)
    {
        $this->videos->removeElement($video);
        $video->setTrick(null);
    }

    /**
     * Get videos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }
}
