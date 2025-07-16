<?php

namespace App\Controller\Authentication;

use App\Controller\BaseController;
use App\Repository\UserRepository;
use App\Service\Authentication\OtpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordController extends BaseController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly OtpService $otpService,
    ) {}

    #[Route('/api/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    public function forgot(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $validator->validate($data, new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()]
        ]));

        if (count($errors) > 0) {
            return $this->error('Validation failed.', null, 400);
        }

        $user = $this->userRepository->findOneBy(['email' => $data['email']]);

        if (!$user || !$user->isVerified()) {
            return $this->error('Email does not exist or is not verified.', null, 400);
        }

        $resetToken = bin2hex(random_bytes(32));
        $user->setResetPasswordToken($resetToken);
        $user->setResetPasswordRequestedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        // Build your frontend link
        $resetLink = "$resetToken";

        // Use your mailer service
        $this->otpService->sendResetPasswordEmail($user->getEmail(), $resetLink);

        return $this->success('Password reset token sent. Check your email.');
    }
}
