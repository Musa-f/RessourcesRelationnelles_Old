<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 *
 * @method File|null find($id, $lockMode = null, $lockVersion = null)
 * @method File|null findOneBy(array $criteria, array $orderBy = null)
 * @method File[]    findAll()
 * @method File[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function createFile($fileData, $ressource)
    {
        $entityManager = $this->getEntityManager();
        
        $file = new File();
        $file->setName($fileData->getClientOriginalName());
        $file->setSize(5);
        $file->setRessource($ressource);
        $entityManager->persist($file);
        $entityManager->flush();

        $uploadDirectory = 'uploads/' . $ressource->getId(); 
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        $filePath = $uploadDirectory . '/' . $fileData->getClientOriginalName();
        $fileData->move($uploadDirectory, $fileData->getClientOriginalName());
    }

}
