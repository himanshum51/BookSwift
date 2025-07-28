<?php

namespace App\Service\Contact;

use App\Entity\SupportMessage;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class ContactService
{
    private UserRepository $userRepository;
    private MailerInterface $mailer;
    private Environment $twig;

    public function __construct(UserRepository $userRepository, MailerInterface $mailer, Environment $twig)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendToAdmins(SupportMessage $supportMessage): void
    {
        $admins = $this->userRepository->findAllAdmins();

        foreach ($admins as $admin) {
            $email = (new Email())
                ->from($supportMessage->getEmail())
                ->to($admin->getEmail())
                ->subject('[Contact Us] ' . $supportMessage->getSubject())
                ->html(
                    $this->twig->render('contact/contact_message.html.twig', [ 
                        'supportMessage' => $supportMessage,
                    ])
                );

            $this->mailer->send($email);
        }
    }
}
