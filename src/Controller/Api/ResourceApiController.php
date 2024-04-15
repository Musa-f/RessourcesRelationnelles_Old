<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Entity\File;
use App\Entity\Format;
use App\Entity\Ressource;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ResourceApiController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/resources', name: 'api_resources', methods: ['GET'])]
    public function getResources(Request $request): JsonResponse
    {
        $currentUser = $this->getUser();
        $page = $request->query->getInt('page', 1); 
        $limit = $request->query->getInt('limit', 2); 

        $resources = $this->entityManager->getRepository(Ressource::class)
            ->findResourcesByVisibility($currentUser ? $currentUser : null, $page, $limit);

        $totalResources = count($resources);

        return $this->json([
            'total' => $totalResources,
            'page' => $page,
            'limit' => $limit,
            'data' => $resources
        ], 200, [], [
            'groups' => [
                'resource.index', 
                'category.index', 
                'format.index',
                'file.index'
            ]
        ]);
    }

    #[Route('/api/resources', name: 'api_create_resource', methods: ['POST'])]
    public function createResource(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $file = $request->files->get('file');
            $sharedUsers = [];
            if(!empty($data['users'])) {
                foreach($data['users'] as $sharedIdUser) {
                    $sharedUsers[] = $this->entityManager->getRepository(User::class)->find($sharedIdUser);
                }
            }

            $format = $this->entityManager->getRepository(Format::class)->find($data['format']);
            $category = $this->entityManager->getRepository(Category::class)->find($data['category']);
            $ressource = $this->entityManager->getRepository(Ressource::class)->createRessource($data, $format, $category, $this->getUser(), $sharedUsers);

            if(!empty($file)) {
                $this->entityManager->getRepository(File::class)->createFile($file, $ressource);
            }
    
            return new JsonResponse(['success' => true, 'message' => 'Ressource créée avec succès']);
        } catch (ORMException $e) {
            return new JsonResponse(['success' => false, 'error' => 'Erreur lors de la création de la ressource : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/api/ressources/non-active', name: 'api_list_non_active_resources', methods: ['GET'])]
    public function listNonActiveResources(): JsonResponse
    {
        $ressources = $this->entityManager->getRepository(Ressource::class)->findNotActivatedRessources();

        return new JsonResponse($ressources);
    }

    #[Route('/api/ressources/{id}/activate', name: 'api_activate_ressource', methods: ['PATCH'])]
    public function activateRessource(int $id): JsonResponse
    {
        $ressource = $this->entityManager->getRepository(Ressource::class)->find($id);
        if(!$ressource) {
            return new JsonResponse(['error' => 'Ressource introuvable'], 404);
        }

        $ressource->setActive(true);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Ressource activée avec succès']);
    }
}
