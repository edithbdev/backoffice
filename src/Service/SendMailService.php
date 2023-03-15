<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Service for sending emails
 */
class SendMailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send an email
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $template
     * @param array<string, object|string> $context
     * @return void
     */
    public function sendMail(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context,
    ): void {
        // On crée un objet TemplatedEmail
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate("emails/$template.html.twig")
            ->context($context);
        // on envoie l'email
        $this->mailer->send($email);
    }
}
