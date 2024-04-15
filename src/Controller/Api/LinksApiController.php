<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LinksApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/links', name: 'api_links', methods: ['GET'])]
    public function getCategories(Request $request): JsonResponse
    {
        $links = ListService::getLinkTypeService();

        return $this->json($links, 200, []);
    }

    #[Route('/api/link/{id}', name: 'api_link', methods: ['GET'])]
    public function getCategory(Request $request, $id): JsonResponse
    {
        $link = "";
        return $this->json($link, 200, []);
    }
}
