<?php

namespace App\Controller\Authentication;

use App\Controller\BaseController;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VerifyController extends BaseController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
    ) {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/verify', name: 'api_verify_email', methods: ['POST'])]
    public function verify(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;

        if (!$token) {
            return $this->error('Verification token is required.', null, 400);
        }

        $user = $this->userRepository->findOneBy(['verificationToken' => $token]);

        if (!$user) {
            return $this->error('Invalid or expired verification token.', null, 400);
        }

        if ($user->isVerified()) {
            return $this->success('Your email is already verified.');
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $this->entityManager->flush();

        return $this->success('Your email has been verified successfully!');
    }
}
