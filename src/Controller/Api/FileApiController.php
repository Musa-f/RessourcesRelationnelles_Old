<?php

namespace App\Controller\Api;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/file/{idResource}/{idFile}', name: 'api_file', methods: ['GET'])]
    public function getFile(int $idResource, int $idFile): Response
    {
        $file = $this->entityManager->getRepository(File::class)->findOneBy(['id' => $idFile, 'ressource' => $idResource]);
        
        $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $idResource . '/' . $file->getName();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        $mimeType = mime_content_type($filePath);

        $response = new Response(file_get_contents($filePath));

        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $file->getName() . '"');

        return $response;
    }

}
