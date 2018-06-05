<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Entity\Video;
use AppBundle\Entity\Picture;
use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class TrickTest extends TestCase
{
    /**
     * Test the hydratation of the Entity and the relationship between entity.
     */
    public function testEntityTrick()
    {
        $trick = new Trick();

        $image = new Picture();
        $image->setName('5ad5fa2f9d61c');
        $image->setPath('uploads/pictures/5ad5fa2f9d61c.jpeg');
        $trick->addPicture($image);

        $video = new Video();
        $video->setUrl('https://www.youtube.com/watch?v=SQyTWk7OxSI');
        $trick->addVideo($video);

        $category = new Category();
        $category->setName('flip');
        $trick->addCategory($category);

        $user = new User();
        $user->setUsername('zohac');
        $trick->setUser($user);

        $trick->setTitle('cab 1260 stalefish flatspin');
        $trick->setSlug('cab-1260-stalefish-flatspin');
        $trick->setContent('test');
        $trick->setDate(new \Datetime('2018-03-08 14:02:00'));

        $this->assertEquals('cab 1260 stalefish flatspin', $trick->getTitle());
        $this->assertEquals(new \DateTime('2018-03-08 14:02:00'), $trick->getDate());
        $this->assertEquals('test', $trick->getContent());

        foreach ($trick->getPictures() as $picture) {
            $this->assertEquals($image, $picture);
        }
        foreach ($trick->getVideos() as $clip) {
            $this->assertEquals($video, $clip);
        }
        foreach ($trick->getCategories() as $cat) {
            $this->assertEquals($category, $cat);
        }
        $this->assertEquals($user, $trick->getUser());
    }
}
