<?php

namespace App\Service\Profile;

use App\Entity\OrganizerProfile;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OrganizerProfileService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
        private readonly FileUploader $fileUploader,
    ) {}

    public function updateProfile(OrganizerProfile $profile, array $data, ?UploadedFile $photoFile = null): void
    {
        $this->logger->debug('Received form data for profile update', ['data' => $data]);

        if (!empty($data['companyName'])) {
            $profile->setCompanyName($data['companyName']);
        }

        if (!empty($data['bio'])) {
            $profile->setBio($data['bio']);
        }

        if (!empty($data['website'])) {
            $profile->setWebsite($data['website']);
        }

        if (!empty($data['phoneNumber'])) {
            $profile->setPhoneNumber($data['phoneNumber']);
        }

        if (!empty($data['address'])) {
            $profile->setAddress($data['address']);
        }

        if ($photoFile instanceof UploadedFile) {
            $photoPath = $this->fileUploader->uploadProfilePhoto($photoFile);
            $publicUrl = $this->fileUploader->getPublicUrl($photoPath);
            $profile->setPhoto($publicUrl);
        }

        $profile->setUpdatedAt(new \DateTimeImmutable());

        $this->em->flush();
        $this->logger->debug('Organizer profile updated and flushed to DB.');
    }
}
