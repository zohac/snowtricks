<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Picture.
 *
 * @ORM\Table(name="picture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PictureRepository")
 *
 * @UniqueEntity(fields="name", message="Un fichier portant ce nom existe déjà!")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Picture
{
    const DEFAULT_USER = 'images/user.svg';
    const DEFAULT_TRICK = 'images/default.jpg';
    const DEFAULT_HOME = 'images/default.jpg';
    const DEFAULT_ERROR = 'images/error.jpg';
    const LOGO = 'images/logo.png';

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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="headLinePicture", type="boolean", nullable=true)
     *
     * @Assert\Type(
     *     type="bool",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    private $headLinePicture;

    /**
     * @var Trick
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Trick", inversedBy="pictures", cascade={"persist", "remove"})
     *
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Assert\Valid
     */
    private $trick;

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
     * Set name.
     *
     * @param string $name
     *
     * @return Picture
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return Picture
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set headLinePicture.
     *
     * @param bool|null $headLinePicture
     *
     * @return Picture
     */
    public function setHeadLinePicture($headLinePicture = null)
    {
        $this->headLinePicture = $headLinePicture;

        return $this;
    }

    /**
     * Get headLinePicture.
     *
     * @return bool|null
     */
    public function getHeadLinePicture()
    {
        return $this->headLinePicture;
    }

    /**
     * Is headlinePicture.
     *
     * @return bool
     */
    public function isHeadLinePicture()
    {
        return $this->headLinePicture;
    }

    /**
     * Set trick.
     *
     * @param \AppBundle\Entity\Trick $trick
     *
     * @return Image
     */
    public function setTrick(\AppBundle\Entity\Trick $trick)
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * Get trick.
     *
     * @return \AppBundle\Entity\Trick
     */
    public function getTrick()
    {
        return $this->trick;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removePicture()
    {
        // PostRemove => We no longer have the entity's ID => Use the name we saved
        if (file_exists($this->path)) {
            // Remove file
            unlink($this->path);
        }
    }
}
