<?php

// src/AppBundle/DataFixtures/ORM/LoadComment.php

namespace AppBundle\DataFixtures\ORM;

use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use AppBundle\Entity\Comment;

class LoadComment extends AbstractFixture implements OrderedFixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $tricks = $manager
                ->getRepository('AppBundle:Trick')
                ->findAll();

        foreach ($tricks as $trick) {
            $comments = Yaml::parseFile('src/AppBundle/DataFixtures/Data/Comment.yml');

            foreach ($comments as $commentData) {
                $author = $manager
                    ->getRepository('AppBundle:User')
                    ->findOneBy(['username' => $commentData['author']]);

                $comment = new Comment();

                $comment->setUser($author);
                $comment->setTrick($trick);
                $comment->setContent($commentData['content']);
                $comment->setDate(new \DateTime($commentData['date']));

                // On la persiste
                $manager->persist($comment);
            }
        }
        // On déclenche l'enregistrement de toutes les commentaires
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}
