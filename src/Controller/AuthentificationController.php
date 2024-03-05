<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\AuthentificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class AuthentificationController extends AbstractController
{
    private $userPasswordHasher;
    private $entityManager;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, AuthentificationService $authService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            try 
            {
                $authService->validateUniqueness(
                    $form->get('login')->getData(),
                    $form->get('email')->getData()
                );

                $authService->validatePasswordStrength($form->get('password')->getData());

                $authService->registerUser(
                    $form->get('login')->getData(),
                    $form->get('email')->getData(),
                    $form->get('password')->getData()
                );

                return $this->redirectToRoute('app_index');
            }
            catch (\InvalidArgumentException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }  
        }

        return $this->render('authentification/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('/');
        }else{
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('authentification/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        } 
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('/');
    }
}
