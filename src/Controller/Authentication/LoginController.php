<?php

namespace App\Controller\Authentication;

use App\Controller\BaseController;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

class LoginController extends BaseController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        ValidatorInterface $validator,
        UserProviderInterface $userProvider,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshTokenManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate input
        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank()],
        ]);

        $errors = $validator->validate($data, $constraints);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->error('Validation failed', $errorMessages, 400);
        }

        // Find user
        $user = $userProvider->loadUserByIdentifier($data['email']);

        if (!$user) {
            return $this->error('Invalid credentials', null, 401);
        }
        // Check if user is verified


        if (!$user->isVerified()) {
            return $this->error(
                'Your email is not verified. Please check your inbox and verify your account before logging in.',
                null,
                401
            );
        }

        // Check password
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->error('Invalid credentials', null, 401);
        }

        // Generate JWT token
        $token = $jwtManager->create($user);

        // Generate refresh token
        $refreshToken = new RefreshToken();
        $refreshToken->setUsername($user->getUserIdentifier());
        $refreshToken->setRefreshToken(bin2hex(random_bytes(64)));
        $refreshToken->setValid((new \DateTime())->modify('+1 month'));

        $refreshTokenManager->save($refreshToken);

        return $this->success('Login successful', [
            'token' => $token,
            'refresh_token' => $refreshToken->getRefreshToken(),
        ],201);
    }
}
