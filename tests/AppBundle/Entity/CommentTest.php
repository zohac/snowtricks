<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class CommentTest extends TestCase
{
    /**
     * Test the hydratation of the Entity.
     */
    public function testEntityComment()
    {
        $comment = new Comment();

        $trick = new Trick();
        $comment->setTrick($trick);

        $user = new User();
        $user->setUsername('zohac');
        $comment->setUser($user);

        $comment->setContent('test');
        $comment->setDate(new \Datetime('2018-03-08 14:02:00'));

        $this->assertEquals(new \DateTime('2018-03-08 14:02:00'), $comment->getDate());
        $this->assertEquals('test', $comment->getContent());
        $this->assertEquals($user, $comment->getUser());
        $this->assertEquals($trick, $comment->getTrick());
    }
}
