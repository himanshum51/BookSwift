<?php

namespace App\Controller\Authentication;

use App\Controller\BaseController;
use App\Service\Authentication\RegisterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends BaseController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        RegisterService $registerService,
        ValidatorInterface $validator,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validate root fields directly using Collection constraint
        $constraints = new Assert\Collection([
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length(min: 8, max: 64),
            ],
            'role' => [
                new Assert\NotBlank(),
                new Assert\Choice(choices: ['ROLE_USER', 'ROLE_ORGANIZER']),
            ],
            'profile' => new Assert\Optional(new Assert\Type('array')),
        ]);

        $errors = $validator->validate($data, $constraints);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->error('Validation failed', $errorMessages);
        }

        // Check if user already exists
        $existingUser = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->error('Email already registered', null, 400);
        }

        try {
            $user = $registerService->register($data);
            return $this->success(
                'Registration successful! Please check your email to verify your account before logging in.',
                [
                    'email' => $user->getEmail(),
                    'role' => $user->getRoles(),
                ],
                201
            );
        } catch (\Exception $e) {
            return $this->error('Registration failed', $e->getMessage(), 500);
        }

    }
}
