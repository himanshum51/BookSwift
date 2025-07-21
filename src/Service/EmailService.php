<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(private readonly MailerInterface $mailer) {}

    public function sendTicketWithPdf(User $user, Event $event, string $pdfContent): void
    {
        $email = (new Email())
            ->from('no-reply@yourapp.com')
            ->to($user->getEmail())
            ->subject('Your ticket for: ' . $event->getTitle())
            ->html('<p>Attached is your ticket for ' . $event->getTitle() . '</p>')
            ->attach($pdfContent, 'ticket.pdf', 'application/pdf');

        $this->mailer->send($email);
    }
}
