<?php

namespace tests\AppBundle\Repository;

use AppBundle\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TrickRepositoryTest extends KernelTestCase
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

    public function testFindTrickWithAllEntities()
    {
        $trick = $this->entityManager
            ->getRepository(Trick::class)
            ->findWithAllEntities('backside-blunt-270')
        ;

        $this->assertContainsOnlyInstancesOf(Trick::class, [$trick]);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}