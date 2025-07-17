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
        $profile = $this->getUser()->getUserProfile();

        return $this->json([
            'id' => $profile->getId(),
            'firstName' => $profile->getFirstName(),
            'lastName' => $profile->getLastName(),
            'phoneNumber' => $profile->getPhoneNumber(),
            'photo' => $profile->getPhoto() ? $this->fileUploader->getPublicUrl($profile->getPhoto()) : null,
        ]);
    }

    #[Route('', methods: ['POST'])] // Accept only POST, and override with _method=PUT in form
    public function updateProfile(Request $request): JsonResponse
    {
        $profile = $this->getUser()->getUserProfile();
        $this->profileService->updateProfile($profile, $request);

        return $this->json(['message' => 'Profile updated!']);
    }
}
