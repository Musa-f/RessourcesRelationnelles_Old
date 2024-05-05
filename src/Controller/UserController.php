<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserRepository; 
use App\Repository\RessourceRepository; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\MailService;



class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

    #[Route('/user/private-chat', name: 'app_private_chat')]
    public function privateChat(): Response
    {
        return $this->render('user/private_chat.html.twig');
    }

    #[Route('/user/likedRessource', name: 'app_likedRessource')]
    public function likedRessource(): Response
    {
        return $this->render('user/likedRessource.html.twig');
    }

    #[Route('/user/savedRessource', name: 'app_savedRessource')]
    public function savedRessource(): Response
    {
        return $this->render('user/savedRessource.html.twig');
    }

    #[Route('/user/app_desactivateAccount', name: 'app_desactivateAccount')]
    public function desactivateAccount(): Response
    {
        return $this->render('user/desactivateAccount.html.twig');
    }
 
    #[Route('/user/app_deleteAccount', name: 'app_deleteAccount')]
    public function deletedAccount(): Response
    {
        return $this->render('user/deleteAccount.html.twig');
    }    
    
    #[Route('/user/app_changePassword', name: 'app_changePassword')]
    public function changedPassword(): Response
    {
        return $this->render('user/changePassword.html.twig');
    }    
    
    #[Route('/user/app_changeLogin', name: 'app_changeLogin')]
    public function changedLogin(): Response
    {
        return $this->render('user/changeLogin.html.twig');
    }








    #[Route('/user/index/active_notification/{userId}', name: 'active_notification')]
    public function active_notification($userId, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $currentState = $user->getNotification();

        $entityManager = $userRepository->getEntityManager();

        if ($currentState == 1) {
            $user->setNotification(0);
        } else {
            $user->setNotification(1);
        }

        $entityManager->flush();

        return new Response((string) $user->getNotification());
    }

    #[Route('/user/index/active_messageMail/{userId}', name: 'active_messageMail')]
    public function active_messageMail($userId, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $currentState = $user->getMessageMail();

        $entityManager = $userRepository->getEntityManager();

        if ($currentState == 1) {
            $user->setMessageMail(0);
        } else {
            $user->setMessageMail(1);
        }

        $entityManager->flush();

        return new Response((string) $user->getMessageMail());
    }

    #[Route('/user/index/ressource_liked/{userId}', name: 'ressource_liked')]
    public function ressourceLiked($userId, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $ressourcesAimees = $user->getLiked();

        return $this->render('user/likedRessource.html.twig', [
            'ressourcesAimees' => $ressourcesAimees,
        ]);
    }

    #[Route('/user/index/ressource_saved/{userId}', name: 'ressource_saved')]
    public function ressourceSaved($userId, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $ressourcesEnregistrees = $user->getSaved();

        return $this->render('user/savedRessource.html.twig', [
            'ressourcesEnregistrees' => $ressourcesEnregistrees,
        ]);
    }

//fonction veifier le mots de passe
    #[Route('/user/index/password_verified/{userId}', name: 'password_verified')]
    public function verifyPassword($userId, Request $request, UserRepository $userRepository)
    {
        $password = $request->request->get('password');

        $user = $userRepository->find($userId);
        
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
        
        $hashedPassword = $user->getPassword();
        
        if (password_verify($password, $hashedPassword)) {
            return new Response('Le mot de passe est correct.', Response::HTTP_OK);
        } else {
            return new Response('Le mot de passe est incorrect.', Response::HTTP_FORBIDDEN);
        }
    }

//fonction verifier le mail
    #[Route('/user/index/email_verified/{userId}', name: 'email_verified')]
    public function verifyEmail($userId, Request $request, UserRepository $userRepository)
    {
        $email = $request->request->get('email');
        $user = $userRepository->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        $storedEmail = $user->getEmail();

        if ($email === $storedEmail) {
            $this->generateReinitCode($userId, $userRepository);
            return $this->json ('Le mail est correct.', 200);

        } else {
            return $this->json('Le mail est incorrect.', 403);
        }
    }

//fonction verifier le CR
    #[Route('/user/index/codeReinit_verified/{userId}', name: 'codeReinit_verified')]
    public function verifyReinitCode($userId, Request $request, UserRepository $userRepository): Response
    {
        $codeReinit = $request->request->get('codeReinit');
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        if ($user->getCodeReinit() == $codeReinit) {
            return new Response('Le code reinitialisation est correct.', Response::HTTP_OK);
        } else {
            return new Response('Le code reinitialisation est incorrect.', Response::HTTP_FORBIDDEN);
        }
    }


    #[Route('/user/index/codeReinit_generated/{userId}', name: 'codeReinit_generated')]
    public function generateReinitCode($userId, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        $reinitCode = mt_rand(100000, 999999);
    
        $user->setCodeReinit($reinitCode);
    
        $entityManager = $userRepository->getEntityManager();
        $entityManager->flush();
  //      MailService::reinitCodeMail($user->getEmail(), $reinitCode);
    
        return new Response('Code généré avec succès.', Response::HTTP_OK); 
    }







//Fonction changer de mot de passe
    #[Route('/user/index/password_changed/{userId}', name: 'password_changed')]
    public function changePassword(Request $request, $userId, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $newPassword = $request->request->get('new_password');

        if (empty($newPassword)) {
            $jsScript = '<script>alert("Le nouveau mot de passe ne peut pas être vide.");</script>';
            return new Response($jsScript);        }

        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);

        $user->setPassword($hashedPassword);

        $entityManager = $userRepository->getEntityManager();
        $entityManager->flush();

        $jsScript = '<script>alert("Le mot de passe a été changé avec succès.");</script>';

        return new Response($jsScript . $this->redirectToRoute('app_user'));
    }

//Fonction changer d'identifiant
    #[Route('/user/index/login_changed/{userId}', name: 'login_changed')]
    public function changeLogin(Request $request, $userId, UserRepository $userRepository)
    {
        $user = $userRepository->find($userId);
    
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        $newLogin = $request->request->get('new_login');
    
        if (empty($newLogin)) {
            $jsScript = '<script>alert("Le nouvel identifiant ne peut pas être vide.");</script>';
            return new Response($jsScript);
        }
    
        $existingUser = $userRepository->findOneBy(['login' => $newLogin]);
        if ($existingUser) {
            $jsScript = '<script>alert("Cet identifiant est déjà utilisé par un autre utilisateur.");</script>';
            return new Response($jsScript);
        }
    
        $user->setLogin($newLogin);
    
        $entityManager = $userRepository->getEntityManager();
        $entityManager->flush();

        $jsScript = '<script>alert("L\'identifiant a été changé avec succès.");</script>';

        return new Response($jsScript . $this->redirectToRoute('app_user'));
    }

//Fonction desactiver le compte
    #[Route('/user/index/account_desactivated/{userId}', name: 'account_desactivated')]
    public function deactivateAccount($userId, UserRepository $userRepository)
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $user->setActive(false);

        $entityManager = $userRepository->getEntityManager();
        $entityManager->flush();

        $jsScript = '<script>alert("Votre compte a été désactivé avec succès.");</script>';

        return new Response($jsScript . $this->redirectToRoute('app_logout'));
    }

//Fonction supprimer le compte
    #[Route('/user/index/account_deleted/{userId}', name: 'account_deleted')]
    public function deleteAccount($userId, UserRepository $userRepository)
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $entityManager = $userRepository->getEntityManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $jsScript = '<script>alert("Votre compte a été supprimé avec succès.");</script>';

        return new Response($jsScript . $this->redirectToRoute('app_logout'));
    }
}
