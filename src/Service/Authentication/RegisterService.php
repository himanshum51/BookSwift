<?php

namespace App\Service\Authentication;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\OrganizerProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
        private readonly OtpService $otpService,
    ) {}

    public function register(array $data): User
    {
        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles([$data['role']]);

        $hashedPassword = $this->hasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $now = new \DateTimeImmutable();
        $user->setCreatedAt($now);
        $user->setUpdatedAt($now);

        # Generate OTP for email verification
        $this->otpService->generateAndSendEmailVerificationOtp($user);

        $profileData = $data['profile'] ?? [];

        if ($data['role'] === 'ROLE_USER') {
            $profile = new UserProfile();
            $profile->setUser($user);
            $profile->setFirstName($profileData['first_name'] ?? null);
            $profile->setLastName($profileData['last_name'] ?? null);
            $profile->setPhoneNumber($profileData['phone_number'] ?? null);
            $profile->setCreatedAt($now);
            $profile->setUpdatedAt($now);

            $user->setUserProfile($profile);
            $this->em->persist($profile);

        } elseif ($data['role'] === 'ROLE_ORGANIZER') {
            $profile = new OrganizerProfile();
            $profile->setUser($user);
            $profile->setCompanyName($profileData['company_name'] ?? null);
            $profile->setBio($profileData['bio'] ?? null);
            $profile->setWebsite($profileData['website'] ?? null);
            $profile->setPhoneNumber($profileData['phone_number'] ?? null);
            $profile->setAddress($profileData['address'] ?? null);
            $profile->setCreatedAt($now);
            $profile->setUpdatedAt($now);

            $user->setOrganizerProfile($profile);
            $this->em->persist($profile);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
