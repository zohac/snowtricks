<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

/**
 * UserRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Recovers a user by token.
     *
     * @param string $token
     *
     * @return User|null
     */
    public function getUserWithToken(string $token): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.token = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}