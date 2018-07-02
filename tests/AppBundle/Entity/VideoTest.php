<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VideoTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Test the hydratation of the Entity.
     */
    public function testEntityVideoYoutube()
    {
        $trick = new Trick();

        $video = new Video();
        $video->setUrl('https://www.youtube.com/watch?v=SQyTWk7OxSI');
        $video->setTrick($trick);

        $this->assertEquals('https://www.youtube.com/watch?v=SQyTWk7OxSI', $video->getURL());
        $this->assertEquals($trick, $video->getTrick());
        $this->assertContains('https://www.youtube.com/embed/', $video->getIframe());
        $this->assertContains('https://img.youtube.com/vi/', $video->getThumbnail());
    }

    /**
     * Test the hydratation of the Entity.
     */
    public function testEntityVideoVimeo()
    {
        $video = new Video();
        $video->setUrl('https://vimeo.com/83896648');

        $this->assertContains('https://player.vimeo.com/video/', $video->getIframe());
        $this->assertContains('https://i.vimeocdn.com/video/', $video->getThumbnail());
    }

    /**
     * Test the hydratation of the Entity.
     */
    public function testEntityVideoDailyMotion()
    {
        $video = new Video();
        $video->setUrl('https://www.dailymotion.com/video/x6hefs5');

        $this->assertContains('//www.dailymotion.com/embed/video/', $video->getIframe());
        $this->assertContains('https://www.dailymotion.com/thumbnail/', $video->getThumbnail());
    }

    /**
     * Test the id of the Entity.
     */
    public function testIdVideo()
    {
        $video = $this->entityManager
            ->getRepository(Video::class)
            ->findOneByUrl('https://www.youtube.com/watch?v=SQyTWk7OxSI')
        ;

        $this->assertInternalType('int', $video->getId());
    }

    /**
     * Test the hydratation of the Entity.
     */
    public function testGetIframeWithInvalidUrl()
    {
        $video = new Video();
        $video->setUrl('AnInvalidUrl');

        $this->assertNull($video->getIframe());
        $this->assertNull($video->getThumbnail());
    }
}
