<?php

namespace App\Controller\Api;

use App\Entity\Format;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FormatApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/formats', name: 'api_formats', methods: ['GET'])]
    public function getFormats(Request $request): JsonResponse
    {
        $formats = $this->entityManager->getRepository(Format::class)->findAll();
        return $this->json($formats, 200, [], [
            'groups' => ['format.index']
        ]);
    }
}
