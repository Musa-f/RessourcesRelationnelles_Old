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

    public function createRessource($data, $format, $category, $user, $sharedUsers)
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

            if(!empty($sharedUsers)){
                foreach($sharedUsers as $sharedUser){
                    $ressource->addShare($sharedUser);
                }
            }

            if(!empty($data['content']))
                $ressource->setContent($data['content']);
    
            $entityManager->persist($ressource);
            $entityManager->flush();
    
            return $ressource;
        } catch (ORMException $e) {
            throw new \Exception("Erreur lors de la création de la ressource : " . $e->getMessage());
        }
    }

    public function findNotActivatedRessources()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin(User::class, 'u', 'WITH', 'u.active = 1')
            ->andWhere('r.active = 0')
            ->getQuery()
            ->getResult();
    }

    public function findResourcesByVisibility($user = null, $page = 1, $limit = 3)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.visibility = :publicVisibility')
            ->andWhere('r.active = 1')
            ->setParameter('publicVisibility', 2)
            ->orderBy('r.creationDate', 'DESC');

        if ($user) {
            $qb->orWhere('r.visibility = :privateVisibility AND r.user = :userId')
               ->setParameter('privateVisibility', 0)
               ->setParameter('userId', $user->getId());

            $qb->orWhere(':user MEMBER OF r.shares AND r.visibility = :sharedVisibility')
               ->setParameter('sharedVisibility', 1)
               ->setParameter('user', $user->getId());

            $qb->orWhere('r.user = :user')
                ->setParameter('user', $user->getId());
        }

        $qb->setMaxResults($limit)
        ->setFirstResult(($page - 1) * $limit);

        return $qb->getQuery()->getResult();
    }

    public function searchResources($category, $link, $keyword)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.category', 'c')
            ->where('r.title LIKE :keyword OR r.content LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%');

        if ($category) {
            $queryBuilder->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $category);
        }

        if ($link) {
            $queryBuilder->andWhere('r.type = :link')
                ->setParameter('link', $link);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
