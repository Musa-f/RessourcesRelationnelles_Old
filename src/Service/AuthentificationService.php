<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthentificationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validateUniqueness($login, $email)
    {
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
        if ($existingUser !== null) {
            throw new \InvalidArgumentException('Login already exists.');
        }

        $existingEmail = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existingEmail !== null) {
            throw new \InvalidArgumentException('Email already exists.');
        }
    }

    public function validatePasswordStrength(string $password)
    {
        if (strlen($password) < 8) {
            throw new \InvalidArgumentException('Password should be at least 8 characters long.');
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new \InvalidArgumentException('Password should contain at least one uppercase letter.');
        }
        if (!preg_match('/[a-z]/', $password)) {
            throw new \InvalidArgumentException('Password should contain at least one lowercase letter.');
        }
        if (!preg_match('/\d/', $password)) {
            throw new \InvalidArgumentException('Password should contain at least one digit.');
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            throw new \InvalidArgumentException('Password should contain at least one special character.');
        }
    }
}
