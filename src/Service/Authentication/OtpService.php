<?php

namespace App\Service\Authentication;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class OtpService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
    ) {}

    /**
     * Generate, store, and send an OTP for email verification.
     */
    public function generateAndSendEmailVerificationOtp(User $user): void
    {
        $token = bin2hex(random_bytes(16)); // Secure 32-char hex
        $user->setVerificationToken($token);
        $user->setIsVerified(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendEmailVerification($user);
    }

    /**
     * Send the verification email using a custom template.
     */
    private function sendEmailVerification(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@yourapp.com')
            ->to($user->getEmail())
            ->subject('Verify your account')
            ->htmlTemplate('authentication/verify_email.html.twig')
            ->context([
                'user' => $user,
                'verificationToken' => $user->getVerificationToken(),
            ]);

        $this->mailer->send($email);
    }

    /**
     * ✅ NEW: Send the reset password email.
     */
    public function sendResetPasswordEmail(string $to, string $resetLink): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@yourapp.com')
            ->to($to)
            ->subject('Reset your password')
            ->htmlTemplate('authentication/reset_password_email.html.twig')
            ->context([
                'resetLink' => $resetLink,
            ]);

        $this->mailer->send($email);
    }
}
