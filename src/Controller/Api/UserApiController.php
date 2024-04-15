<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailService;
use App\Service\SecurityService;
use App\Service\TokenGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserApiController extends AbstractController
{
    private $userPasswordHasher;
    private $entityManager;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, SecurityService $authService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($data);

        if ($form->isValid()) 
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

                return new JsonResponse(['success' => true, 'message' => 'Votre compte a été créé avec succès. Veuillez confirmer votre adresse e-mail pour activer votre compte.'], 201);
            }
            catch (\InvalidArgumentException $exception) {
                return new JsonResponse(['success' => false, 'error' => $exception->getMessage()], 400);
            }  
        }

        return new JsonResponse(['success' => false, 'error' => 'Données invalides'], 400);
    }

    #[Route(path: '/api/account/activation', name: 'api_activate_account', methods: ['GET'])]
    public function activateAccount(Request $request): JsonResponse
    {
        $activationToken = $request->query->get('token');
    
        if(!empty($activationToken)) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $activationToken]);

            if ($user) {
                $user->setActive(true);
                $user->setToken(null);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                
                return new JsonResponse(['success' => true, 'message' => 'Votre compte a été activé avec succès. Vous pouvez vous connecter dès maintenant.']);
            }
        }

        return new JsonResponse(['success' => false, 'error' => 'Une erreur est survenue'], 404);
    }

    #[Route(path: '/api/login/', name: 'api_login', methods:['GET'])]
    public function login(Request $request): JsonResponse
    {
        return $this->json();
    }

    #[Route('api/users', name: 'api_gets_users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        $currentUser = $this->getUser();

        if($currentUser)
            $users = $this->entityManager->getRepository(User::class)->findAllExceptCurrentUser($currentUser->getId());
        else
            $users = null;

        return $this->json($users, 200, [], [
            'groups' => [
                'user.index'
            ]
        ]);
    }

    #[Route('/api/users/{id}', name: 'api_get_user', methods: ['GET'])]
    public function retrieveUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }
        return new JsonResponse($user);
    }

    #[Route('/api/users/{id}', name: 'api_update_user', methods: ['PUT'])]
    public function updateUser(int $id, Request $request): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Utilisateur mis à jour avec succès']);
    }

    #[Route('/api/users/{id}', name: 'api_delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Utilisateur supprimé avec succès']);
    }
}
