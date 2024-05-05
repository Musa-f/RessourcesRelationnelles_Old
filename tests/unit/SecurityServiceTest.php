<?php

namespace App\Tests\Service;

use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SecurityServiceTest extends TestCase
{
    public function testValidPassword()
    {
        $entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $authService = new SecurityService($entityManagerMock);
        $password = 'StrongPassword1$';

        $this->assertNull($authService->validatePasswordStrength($password));
    }
}