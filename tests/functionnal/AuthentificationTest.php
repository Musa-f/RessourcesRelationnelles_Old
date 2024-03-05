<?php

use App\Entity\User;
use App\Service\AuthentificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthentificationTest extends KernelTestCase
{
    private $userPasswordHasher;
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();

        $this->userPasswordHasher = $container->get(UserPasswordHasherInterface::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);
    }

    public function testRegisterUser(): void
    {
        $userLogin = "test";
        $userEmail = "test@example.com";
        $userPassword = "123456789";

        $authService = new AuthentificationService($this->userPasswordHasher, $this->entityManager);

        $modelUser = $authService->registerUser(
            $userLogin,
            $userEmail,
            $userPassword
        );

        $user = $this->entityManager->getRepository(User::class)->find($modelUser->getId());
        
        $this->assertNotNull($modelUser->getId());
        $this->assertNotEquals($userPassword, $user->getPassword());
    }
}
