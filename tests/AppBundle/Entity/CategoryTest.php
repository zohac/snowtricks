<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CategoryTest extends KernelTestCase
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
    public function testEntityComment()
    {
        $category = new Category();
        $category->setName('Un test');

        $this->assertEquals('Un test', $category->getName());
    }

    /**
     * Test the id of the Entity.
     */
    public function testIdComment()
    {
        $category = $this->entityManager
            ->getRepository(Category::class)
            ->findOneByName('flip')
        ;

        $this->assertInternalType('int', $category->getId());
    }
}
