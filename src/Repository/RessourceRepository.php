<?php

namespace App\Repository;

use App\Entity\Ressource;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ressource>
 *
 * @method Ressource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ressource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ressource[]    findAll()
 * @method Ressource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RessourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ressource::class);
    }

    public function createRessource($data, $format, $category, $user)
    {
        try {
            $entityManager = $this->getEntityManager();
            $ressource = new Ressource();
            
            $ressource->setTitle($data['title']);
            $ressource->setCreationDate(new DateTime());
            $ressource->setType($data['link']);
            $ressource->setVisibility($data['visibility']);
            $ressource->setActive(0);
            $ressource->setFormat($format);
            $ressource->setCategory($category);
            $ressource->setUser($user);

            if(!empty($data['content']))
                $ressource->setContent($data['content']);
    
            $entityManager->persist($ressource);
            $entityManager->flush();
    
            return $ressource;
        } catch (ORMException $e) {
            throw new \Exception("Erreur lors de la création de la ressource : " . $e->getMessage());
        }
    }

    //*** visiblesRessources
    //contient toutes les ressources en publique (visibility à 2)
    //contient toutes les ressources en privées (visiblity à 0) si l'id user rattaché à la ressource est le même que celui de la session en cours (celui passé en argument à la méthode)
    //contient toutes les ressources en partagées (visibility à 1) si l'id du user est dans la table "shares"

    public function findNotActivatedRessources()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin(User::class, 'u', 'WITH', 'u.active = 1')
            ->andWhere('r.active = 0')
            ->getQuery()
            ->getResult();
    }

    public function findResourcesByVisibility($userId)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.visibility = :publicVisibility')
            ->setParameter('publicVisibility', 2);

        $user = $this->security->getUser();
        if ($user) {
            $qb->orWhere('r.visibility = :privateVisibility AND r.user = :userId')
               ->setParameter('privateVisibility', 0)
               ->setParameter('userId', $userId);

            $qb->orWhere(':user MEMBER OF r.shares AND r.visibility = :sharedVisibility')
               ->setParameter('sharedVisibility', 1)
               ->setParameter('user', $user);
        }

        return $qb->getQuery()->getResult();
    }
}
