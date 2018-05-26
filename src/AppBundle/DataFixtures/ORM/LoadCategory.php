<?php

// src/AppBundle/DataFixtures/ORM/LoadCategory.php

namespace AppBundle\DataFixtures\ORM;

use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use AppBundle\Entity\Category;

class LoadCategory extends AbstractFixture implements OrderedFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        // Liste des noms de catégorie à ajouter
        $categories = Yaml::parseFile('src/AppBundle/DataFixtures/Data/Category.yml');

        foreach ($categories as $categoryData) {
            $category = new Category();

            $category->setName($categoryData['name']);

            // On la persiste
            $manager->persist($category);
        }

        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
