<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function createUser($entityManager, $userPasswordHasher, $login, $email, $password, $token, $active = 0, $role = null)
    {
        $user = new User();
        $user->setLogin($login);
        $user->setEmail($email);
        $user->setCreationDate(new DateTime());
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setToken($token);
        $user->setActive($active);

        if(isset($role))
            $user->setRoles($role);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    public function desactiveUser()
    {}

    public function deleteUser()
    {}

    public function findAllExceptCurrentUser($currentUserId)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :currentUserId')
            ->andWhere('u.active = 1')
            ->setParameter('currentUserId', $currentUserId)
            ->getQuery()
            ->getResult();
    }
}
