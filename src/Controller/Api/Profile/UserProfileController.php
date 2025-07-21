<?php

namespace App\Controller\Api\Profile;

use App\Service\Profile\UserProfileService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/profile/user')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserProfileController extends AbstractController
{
    public function __construct(
        private readonly UserProfileService $profileService,
        private readonly FileUploader $fileUploader,
    ) {}

    #[Route('', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        $user = $this->getUser();
        $profile = $user->getUserProfile();

        if (!$profile) {
            return $this->json(['error' => 'Profile not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $profile->getId(),
            'firstName' => $profile->getFirstName(),
            'lastName' => $profile->getLastName(),
            'phoneNumber' => $profile->getPhoneNumber(),
            'photo' => $profile->getPhoto() ? $this->fileUploader->getPublicUrl($profile->getPhoto()) : null,
        ]);
    }

    #[Route('', methods: ['POST'])]
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $profile = $user->getUserProfile();

        if (!$profile) {
            return $this->json(['error' => 'Profile not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $this->profileService->updateProfile($profile, $request);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Unable to update profile',
                'details' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Profile updated!']);
    }
}
