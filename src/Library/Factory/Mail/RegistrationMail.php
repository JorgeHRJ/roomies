<?php

namespace App\Library\Factory\Mail;

class RegistrationMail extends AbstractMail
{
    /**
     * @return string
     */
    protected function getMailTemplate(): string
    {
        return 'mail/confirm_registration.html.twig';
    }

    /**
     * @return string
     */
    protected function getMailSubject(): string
    {
        return 'confirm_registration.subject';
    }
}
