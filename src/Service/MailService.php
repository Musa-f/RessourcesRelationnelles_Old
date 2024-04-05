<?php

namespace App\Service;

use Mailtrap\Config;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Mailtrap\EmailHeader\CategoryHeader;

class MailService
{  
    public static function resetPassword(){}

    public static function activationAccount($recipientEmail, $activationToken)
    {
        $mailtrap = new MailtrapClient(new Config($_ENV['APP_SECRET_MAILTRAP']));

        $activationLink = $_ENV['APP_URL'] . '/account/activation?token=' . $activationToken;

        $email = (new Email())
            ->from(new Address('mailtrap@demomailtrap.com', 'Mailtrap Test'))
            ->to(new Address($recipientEmail))
            ->subject('Activate Your Account')
            ->html("Please click <a href=\"$activationLink\">here</a> to activate your account.")
        ;

        return $mailtrap->sending()->emails()->send($email);
    }
}
