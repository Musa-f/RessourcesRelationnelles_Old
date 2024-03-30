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

    public function ressourceNotActivated()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin(User::class, 'u', 'WITH', 'u.active = 1')
            ->andWhere('r.active = 0')
            ->getQuery()
            ->getResult();
    }
}
