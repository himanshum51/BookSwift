<?php

namespace App\Controller\Api\Profile;

use App\Entity\OrganizerProfile;
use App\Service\Profile\OrganizerProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Uuid;

#[Route('/api/profile/organizer')]
#[IsGranted('ROLE_ORGANIZER')]
class OrganizerProfileController extends AbstractController
{
    public function __construct(
        private readonly OrganizerProfileService $profileService,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    ) {}

    #[Route('', name: 'api_organizer_profile_get', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        $profile = $this->getUser()->getOrganizerProfile();

        if (!$profile) {
            return $this->json(['message' => 'Organizer profile not found'], 404);
        }

        return $this->json([
            'id' => $profile->getId(),
            'companyName' => $profile->getCompanyName(),
            'bio' => $profile->getBio(),
            'website' => $profile->getWebsite(),
            'phoneNumber' => $profile->getPhoneNumber(),
            'address' => $profile->getAddress(),
            'photo' => $profile->getPhoto(),
            'createdAt' => $profile->getCreatedAt(),
            'updatedAt' => $profile->getUpdatedAt(),
        ]);
    }

    #[Route('', name: 'api_organizer_profile_create_or_update', methods: ['PUT','POST'])]
    public function createOrUpdateProfile(Request $request): JsonResponse
    {
        $this->logger->debug('Incoming form-data', [
            'fields' => $request->request->all(),
            'files' => $request->files->all(),
        ]);

        $data = $request->request->all();
        $photo = $request->files->get('photo');

        $user = $this->getUser();
        $profile = $user->getOrganizerProfile();

        if (!$profile) {
            $profile = new OrganizerProfile();
            $profile->setUser($user);
            $user->setOrganizerProfile($profile);
            $this->em->persist($profile);
            $this->em->persist($user);
        }

        $this->profileService->updateProfile($profile, $data, $photo);

        return $this->json(['message' => 'Organizer profile created/updated successfully']);
    }

    #[Route('', name: 'api_organizer_profile_delete', methods: ['DELETE'])]
    public function deleteProfile(): JsonResponse
    {
        $user = $this->getUser();
        $profile = $user->getOrganizerProfile();

        if (!$profile) {
            return $this->json(['message' => 'No profile found to delete'], 404);
        }

        $user->setOrganizerProfile(null);
        $this->em->remove($profile);
        $this->em->flush();

        return $this->json(['message' => 'Organizer profile deleted successfully']);
    }
}
