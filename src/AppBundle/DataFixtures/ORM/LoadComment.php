<?php

// src/AppBundle/DataFixtures/ORM/LoadComment.php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
use Symfony\Component\Yaml\Yaml;
use AppBundle\Listener\CommentListener;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

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

            $listenerInst = null;
            foreach ($manager->getEventManager()->getListeners() as $event => $listeners) {
                foreach ($listeners as $hash => $listener) {
                    if ($listener instanceof CommentListener) {
                        $listenerInst = $listener;
                        break 2;
                    }
                }
            }
            if ($listenerInst) {
                // then you can remove events you like:
                $evm = $manager->getEventManager();
                $evm->removeEventListener(
                    [
                        'prePersist',
                        'preUpdate',
                        'postPersist',
                        'postUpdate',
                    ],
                    $listenerInst
                );
            }

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
