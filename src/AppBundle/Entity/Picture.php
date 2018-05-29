<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @ORM\Column(name="name", type="string", length=100, unique=true)
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
     */
    private $headLinePicture;

    /**
     * Undocumented variable.
     *
     * @Assert\File(
     *      mimeTypes={"image/jpeg", "image/png"},
     *      mimeTypesMessage="Le fichier doit-être du type jpeg ou png.",
     * )
     */
    private $file;

    /**
     * Temporary store the file name.
     *
     * @var string
     */
    private $tempFilename;

    /**
     * @var Trick
     *
     * @ORM\ManyToOne(
     *      targetEntity="AppBundle\Entity\Trick",
     *      cascade={"persist"},
     *      inversedBy="pictures"
     * )
     * @ORM\JoinColumn(nullable=true)
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
     * Get File.
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set Files.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->tempFilename = $this->path;
            $this->path = null;
        }
    }

    /**
     * @return string
     */
    public function getUploadDir(): string
    {
        // On retourne le chemin relatif vers l'image pour un navigateur (relatif au répertoire /web donc)
        return 'uploads/pictures';
    }

    /**
     * @return string
     */
    protected function getUploadRootDir(): string
    {
        // On retourne le chemin relatif vers l'image pour notre code PHP
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        // If no file is set, do nothing
        if (null === $this->file) {
            return;
        }
        $uniqid = uniqid();
        // The file name is the entity's ID
        $this->path = $this->getUploadDir().'/'.$uniqid.'.'.$this->file->guessExtension();
        // And we keep the original name
        $this->name = $uniqid;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // If no file is set, do nothing
        if (null === $this->file) {
            return;
        }

        // A file is present, remove it
        if (null !== $this->tempFilename) {
            if (file_exists($this->tempFilename)) {
                unlink($this->tempFilename);
            }
        }

        // Move the file to the upload folder
        $this->file->move(
            $this->getUploadRootDir(),
            $this->name.'.'.$this->file->guessExtension()
        );

        $this->file = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        // Save the name of the file we would want to remove
        $this->tempFilename = $this->path;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        // PostRemove => We no longer have the entity's ID => Use the name we saved
        if (file_exists($this->tempFilename)) {
            // Remove file
            unlink($this->tempFilename);
        }
    }
}
