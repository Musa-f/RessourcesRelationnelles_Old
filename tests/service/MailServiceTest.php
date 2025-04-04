<?php

namespace App\Tests\Service;

use App\Service\MailService;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class MailServiceTest extends TestCase
{
    public function setUp(): void
    {
        (new Dotenv())->bootEnv(__DIR__.'/../../.env.test');
    }

    public function testActivationAccount()
    {
        $response = MailService::activationAccount($_ENV['APP_RECIPIENT'], 'fakeToken');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
