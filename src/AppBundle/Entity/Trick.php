<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Trick.
 *
 * @ORM\Table(name="trick")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TrickRepository")
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
     * @ORM\JoinColumn(nullable=false)
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

    public function __construct()
    {
        $this->date = new \Datetime('NOW');
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
}
