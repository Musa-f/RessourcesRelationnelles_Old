<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('homepage.html.twig');
    }


    #[Route('/user/private-chat', name: 'app_private_chat')]
    public function privateChat(): Response
    {
        return $this->render('user/private_chat.html.twig');
    }


}