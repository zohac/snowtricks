<?php

namespace tests\AppBundle\Repository;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
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

    public function testGetUserWithEmailRecovery()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->getUserWithemailRecovery('email@test.com')
        ;

        $this->assertContainsOnlyInstancesOf(User::class, [$user]);
    }

    public function testGetUserWithValidToken()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->getUserWithToken('ce9823958a3f4cf450af53e0091d81d60a78874b503c7f210e39b3b6d94b785f')
        ;

        $this->assertContainsOnlyInstancesOf(User::class, [$user]);
    }

    public function testGetUserWithInvalideToken()
    {
        $this->expectException(\LogicException::class);

        $this->entityManager
            ->getRepository(User::class)
            ->getUserWithToken('aaaaaaaaaaaaaaaaaaace9823958a3f4cf450af53e0091d81d60a78874b503c7')
        ;
    }

    public function testGetUserWithAnatherInvalideToken()
    {
        $this->expectException(\LogicException::class);

        $this->entityManager
            ->getRepository(User::class)
            ->getUserWithToken('AnInvalideToken')
        ;
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
