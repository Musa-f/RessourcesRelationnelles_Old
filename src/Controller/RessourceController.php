<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\File;
use App\Entity\Format;
use App\Entity\Ressource;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RessourceController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/ressource', name: 'app_ressource')]
    public function index(): Response
    {
        return $this->render('ressource/index.html.twig', []);
    }

    #[Route('/ressource/create/user', name: 'create_ressource')]
    public function create_ressource(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->request->get('data'), true);
            $file = $request->files->get('file');

            $format = $this->entityManager->getRepository(Format::class)->getFormat($data);
            $category = $this->entityManager->getRepository(Category::class)->getCategory($data);
            $ressource = $this->entityManager->getRepository(Ressource::class)->createRessource($data, $format, $category, $this->getUser());

            if(!empty($file)) {
                $this->entityManager->getRepository(File::class)->createFile($file, $ressource);
            }
    
            return new JsonResponse(['success' => true, 'message' => 'Ressource créée avec succès']);
        } catch (ORMException $e) {
            return new JsonResponse(['success' => false, 'error' => 'Erreur lors de la création de la ressource : ' . $e->getMessage()], 500);
        }
    }

    #[Route('/ressource/non-active/list/moderator', name: 'list_non_active_resources')]
    public function list_non_active_resources(): Response
    {
        $ressources = $this->entityManager->getRepository(Ressource::class)->findNotActivatedRessources();

        return $this->render('moderator/control_ressource.html.twig', [
            "ressources" => $ressources
        ]);
    }

    #[Route('/ressource/non-active/validation/moderator', name: 'validation_non_active_ressources')]
    public function validation_non_active_resources(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $ressourceId = $data['ressourceId'];
        $userId = $data['userId'];

        $ressource = $this->entityManager->getRepository(Ressource::class)->find($ressourceId);
        $ressource->setActive(1);
        $this->entityManager->persist($ressource);
        $this->entityManager->flush();

        $response = [
            'message' => 'Validation de la ressource réussie',
            'status' => 'success'
        ];

        return new JsonResponse($response);
    }
}
