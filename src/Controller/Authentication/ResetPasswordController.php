<?php

namespace App\Controller\Authentication;

use App\Controller\BaseController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPasswordController extends BaseController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

   #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
public function reset(Request $request, ValidatorInterface $validator): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $errors = $validator->validate($data, new Assert\Collection([
        'token' => [new Assert\NotBlank()],
        'password' => [new Assert\NotBlank(), new Assert\Length(min: 8)],
    ]));

    if (count($errors) > 0) {
        return $this->error('Validation failed.', null, 400);
    }

    $user = $this->userRepository->findOneBy(['resetPasswordToken' => $data['token']]);

    if (!$user) {
        return $this->error('Invalid or expired token.', null, 400);
    }

    $expiresAt = $user->getResetPasswordRequestedAt()?->modify('+1 hour');
    if (!$expiresAt || new \DateTimeImmutable() > $expiresAt) {
        return $this->error('Token has expired.', null, 400);
    }

    $hashed = $this->passwordHasher->hashPassword($user, $data['password']);
    $user->setPassword($hashed);
    $user->setResetPasswordToken(null);
    $user->setResetPasswordRequestedAt(null);

    $this->entityManager->flush();

    return $this->success('Your password has been reset successfully.');
}
}
