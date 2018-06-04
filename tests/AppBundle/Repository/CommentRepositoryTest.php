<?php

namespace tests\AppBundle\Repository;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommentRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var Trick
     */
    private $trick;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->trick =  $this->entityManager
            ->getRepository(Trick::class)
            ->findOneBySlug('backside-1440-triple-cork')
        ;
    }

    public function testFindCommentWithAllEntities()
    {
        $comments = $this->entityManager
            ->getRepository(Comment::class)
            ->findWithAllEntities($this->trick)
        ;

        foreach ($comments as $comment) {
            $this->assertEquals($comment->getTrick(), $this->trick);
        }
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
        $this->trick = null;
    }
}