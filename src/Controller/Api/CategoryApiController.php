<?php

namespace App\Controller\Api;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryApiController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/categories', name: 'api_categories', methods: ['GET'])]
    public function getCategories(Request $request): JsonResponse
    {
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return $this->json($categories, 200, [], [
            'groups' => ['category.index']
        ]);
    }

    #[Route('/api/category/{id}', name: 'api_category', methods: ['GET'])]
    public function getCategory(Request $request, $id): JsonResponse
    {
        $category = "";
        return $this->json($category, 200, []);
    }
}
