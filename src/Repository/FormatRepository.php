<?php

namespace App\Repository;

use App\Entity\Format;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Format>
 *
 * @method Format|null find($id, $lockMode = null, $lockVersion = null)
 * @method Format|null findOneBy(array $criteria, array $orderBy = null)
 * @method Format[]    findAll()
 * @method Format[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Format::class);
    }
}
