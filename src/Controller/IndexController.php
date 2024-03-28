<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Format;
use App\Entity\User;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        $currentUser = $this->getUser();
        $categories = $this->entityManager->getRepository(Category::class)->findAll();
        $formats = $this->entityManager->getRepository(Format::class)->findAll();
        
        if($currentUser)
            $users = $this->entityManager->getRepository(User::class)->findAllExceptCurrentUser($currentUser->getId());
        else
            $users = null;


        return $this->render('homepage.html.twig', [
            'categories' => $categories,
            'formats' => $formats,
            'links' => ListService::getLinkTypeService(),
            'users' => $users
        ]);
    }
}
