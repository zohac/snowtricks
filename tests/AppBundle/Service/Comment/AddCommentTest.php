<?php

namespace tests\AppBundle\Service\Comment;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use AppBundle\Service\Comment\AddComment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddCommentTest extends TestCase
{
    private $tokenStorageInterface;
    private $objectManager;
    private $token;

    public function setUp()
    {
        $user = new User();

        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->tokenStorageInterface = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorageInterface
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);
    }

    public function testCreateComment()
    {
        $trick = new Trick();
        $comment = 'A great comment!';

        $AddComment = new AddComment($this->objectManager, $this->tokenStorageInterface);
        $AddComment->add($trick, $comment);
    }

    public function tearDown()
    {
        $this->tokenStorageInterface = null;
        $this->token = null;
        $this->objectManager = null;
    }
}
