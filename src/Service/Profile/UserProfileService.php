<?php

namespace App\Service\Profile;

use App\Entity\UserProfile;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserProfileService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader,
    ) {}

    public function updateProfile(UserProfile $profile, Request $request): void
    {
        $data = $request->request->all();


        if (!empty($data['firstName'])) {
            $profile->setFirstName($data['firstName']);
        }

        if (!empty($data['lastName'])) {
            $profile->setLastName($data['lastName']);
        }

        if (!empty($data['phoneNumber'])) {
            $profile->setPhoneNumber($data['phoneNumber']);
        }

        if ($uploadedFile = $request->files->get('photo')) {
            $path = $this->fileUploader->uploadProfilePhoto($uploadedFile);
            $profile->setPhoto($path);
        }

        $profile->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }
}
