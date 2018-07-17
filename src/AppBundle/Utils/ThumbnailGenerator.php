<?php

namespace AppBundle\Utils;

use AppBundle\Entity\Picture;

class ThumbnailGenerator
{
    /**
     * 576px is the Bootstrap Extra small size.
     *
     * @var int
     */
    private $desiredWidth = 576;

    /**
     * @var [type]
     */
    private $imageSource;

    /**
     * Generate a thumbnail.
     *
     * @param Picture $picture
     */
    public function makeThumb(Picture $picture)
    {
        /* read the source image */
        switch (mime_content_type($picture->getUploadRootDir().'/'.$picture->getName())) {
            case 'image/jpeg':
                $this->jpegThumb($picture);
                break;

            case 'image/png':
                $this->pngThumb($picture);
                break;
        }
    }

    private function jpegThumb(Picture $picture)
    {
        $thumbnail = $this->generateThumb(imagecreatefromjpeg($picture->getUploadRootDir().'/'.$picture->getName()));

        /* create the physical thumbnail image to its destination */
        imagejpeg($thumbnail, $picture->getUploadRootDir().'/thumb_'.$picture->getName());
    }

    private function pngThumb(Picture $picture)
    {
        $thumbnail = $this->generateThumb(imagecreatefrompng($picture->getUploadRootDir().'/'.$picture->getName()));

        /* create the physical thumbnail image to its destination */
        imagepng($thumbnail, $picture->getUploadRootDir().'/thumb_'.$picture->getName());
    }

    private function generateThumb($imageSource)
    {
        $width = imagesx($imageSource);
        $height = imagesy($imageSource);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desiredHeight = floor($height * ($this->desiredWidth / $width));

        /* create a new, "virtual" image */
        $virtualImage = imagecreatetruecolor($this->desiredWidth, $desiredHeight);

        /* copy source image at a resized size */
        imagecopyresampled(
            $virtualImage,
            $imageSource,
            0,
            0,
            0,
            0,
            $this->desiredWidth,
            $desiredHeight,
            $width,
            $height
        );

        return $virtualImage;
    }

    /**
     * Set a desired with for the thumbnail.
     *
     * @param int $desiredWidth
     */
    public function setDesiredWidth(int $desiredWidth)
    {
        $this->desiredWidth = $desiredWidth;
    }
}
