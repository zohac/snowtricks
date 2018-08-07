<?php

// src/AppBundle/DataFixtures/ORM/LoadTrick.php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Trick;
use AppBundle\Entity\Video;
use AppBundle\Entity\Picture;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadTrick extends AbstractFixture implements OrderedFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $tricks = Yaml::parseFile('src/AppBundle/DataFixtures/Data/Trick.yml');

        foreach ($tricks as $trickData) {
            $user = $manager
                ->getRepository('AppBundle:User')
                ->findOneBy(['username' => $trickData['user']]);

            $trick = new Trick();

            foreach ($trickData['image'] as $name) {
                $image = new Picture();
                $image->setName($name);
                $image->setPath('uploads/pictures/'.$name);
                $image->setHeadlinePicture(true);
                $image->setTrick($trick);
                $trick->addPicture($image);
            }

            foreach ($trickData['video'] as $url) {
                $video = new Video();
                $video->setUrl($url);
                $video->setTrick($trick);
                $trick->addVideo($video);
            }

            foreach ($trickData['category'] as $category) {
                $category = $manager
                    ->getRepository('AppBundle:Category')
                    ->findOneBy(['name' => $category]);

                $trick->addCategory($category);
            }

            $trick->setUser($user);
            $trick->setTitle($trickData['title']);
            $trick->setSlug($trickData['slug']);
            $trick->setContent($trickData['content']);
            $trick->setDate(new \DateTime($trickData['date']));

            // On la persiste
            $manager->persist($trick);
        }

        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}
