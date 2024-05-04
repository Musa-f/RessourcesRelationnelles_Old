<?php

namespace App\Service;

use Mailtrap\Config;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Mailtrap\EmailHeader\CategoryHeader;
use Mailtrap\EmailHeader\Template\TemplateUuidHeader;
use Mailtrap\EmailHeader\Template\TemplateVariableHeader;

class MailService
{  
    public static function resetPassword(){}

    public static function activationAccount($recipientEmail, $activationToken)
    {
        $mailtrap = new MailtrapClient(new Config($_ENV['APP_SECRET_MAILTRAP']));

        $activationLink = $_ENV['APP_URL'] . '/account/activation?token=' . $activationToken;

        $email = (new Email())
            ->from(new Address($_ENV['APP_SENDER'], 'Ressources Relationnelles'))
            ->to(new Address($recipientEmail));

        $email->getHeaders()
            ->add(new TemplateUuidHeader('8a2b9f51-78af-4421-8702-992aeddce0aa'))
            ->add(new TemplateVariableHeader('user_email', $recipientEmail))
            ->add(new TemplateVariableHeader('pass_reset_link', $activationLink));

        return $mailtrap->sending()->emails()->send($email);
    }
}
