<?php

// src/AppBundle/DataFixtures/ORM/LoadUser.php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use AppBundle\Entity\Picture;
use Symfony\Component\Yaml\Yaml;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUser extends AbstractFixture implements OrderedFixtureInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        // Liste des noms de catégorie à ajouter
        $users = Yaml::parseFile('src/AppBundle/DataFixtures/Data/User.yml');

        foreach ($users as $userData) {
            $user = new User();

            if (isset($userData['avatar'])) {
                $image = new Picture();
                $image->setName($userData['avatar']);
                $image->setPath('uploads/pictures/'.$userData['avatar'].'.jpeg');

                $user->setAvatar($image);
            }
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $user->setDateRegistration(new \DateTime($userData['dateRegistration']));

            if (isset($userData['token'])) {
                $user->setToken($userData['token']);
            }

            $password = $this->encoder->encodePassword($user, $userData['password']);
            $user->setPassword($password);

            // On la persiste
            $manager->persist($user);
        }

        // On déclenche l'enregistrement de toutes les catégories
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
