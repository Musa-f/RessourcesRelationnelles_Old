<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailService;
use App\Service\SecurityService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function register(Request $request, SecurityService $authService): Response
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

                $token = TokenGeneratorService::generateToken();

                $this->entityManager->getRepository(User::class)->createUser(
                    $this->entityManager,
                    $this->userPasswordHasher,
                    $form->get('login')->getData(),
                    $form->get('email')->getData(),
                    $form->get('password')->getData(),
                    $token
                );

                MailService::activationAccount($form->get('email')->getData(), $token);

                $this->addFlash('success', "Votre compte a été créé avec succès. Veuillez confirmer votre adresse e-mail pour activer votre compte.");
            }
            catch (\InvalidArgumentException $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }  
        }
    
        return $this->render('authentification/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if($this->getUser()){
            return $this->redirectToRoute('/');
        }
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('authentification/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('/');
    }

    #[Route(path: '/account/activation', name: 'app_activate_account')]
    public function activate_acount(Request $request): Response
    {
        $activationToken = $request->query->get('token');
    
        if(!empty($activationToken)) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $activationToken]);

            if ($user) {
                $user->setActive(true);
                $user->setToken(null);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                
                return $this->render('sections/system_messages.html.twig', [
                    "message" => "Votre compte a été activé avec succès. Vous pouvez vous connecter dès maintenant."
                ]);
            }
        }

        return $this->render('sections/system_messages.html.twig', [
            "message" => "Une erreur est survenue"
        ]);
    }
}
