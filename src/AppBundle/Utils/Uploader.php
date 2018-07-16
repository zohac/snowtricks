<?php

namespace AppBundle\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    const UPLOAD_ROOT_DIR = __DIR__.'/../../../web/';
    const UPLOAD_PICTURE_DIR = 'uploads/pictures';

    /**
     * The name of the uploded file.
     *
     * @var string
     */
    private $name;

    /**
     * the path of the uploded file.
     *
     * @var string
     */
    private $path;

    /**
     * Allow to upload a file and save it into the images.uploads.dir.
     *
     * @param UploadedFile $file
     */
    public function uploadFile(UploadedFile $file)
    {
        $this->name = uniqid().'.'.$file->guessExtension();

        $file->move(self::UPLOAD_ROOT_DIR.$this->path, $this->name);
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the path.
     *
     * @param string $path
     *
     * @return Uploader
     */
    public function setPath(string $path): Uploader
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
