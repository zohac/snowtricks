<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class PictureTest extends TestCase
{
    /**
     * Test the hydratation of the Entity.
     */
    public function testEntityPicture()
    {
        $trick = new Trick();

        $picture = new Picture();
        $picture->setName('aGreatName');
        $picture->setPath('uploads/pictures/5ad5fa2f9d61c.jpeg');
        $picture->setHeadLinePicture(true);
        $picture->setTrick($trick);

        $this->assertEquals('aGreatName', $picture->getName());
        $this->assertEquals('uploads/pictures/5ad5fa2f9d61c.jpeg', $picture->getPath());
        $this->assertEquals(true, $picture->getHeadLinePicture());
        $this->assertEquals($trick, $picture->getTrick());
    }
}
