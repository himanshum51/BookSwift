<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Entity\OrganizerProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Create regular users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setIsVerified(true);
            $user->setCreatedAt($faker->dateTimeThisDecade());
            $user->setUpdatedAt($faker->dateTimeThisMonth());

            $userProfile = new UserProfile();
            $userProfile->setUser($user);
            $userProfile->setFirstName($faker->firstName);
            $userProfile->setLastName($faker->lastName);
            $userProfile->setPhoneNumber($faker->phoneNumber);
            $userProfile->setCreatedAt($user->getCreatedAt());
            $userProfile->setUpdatedAt($user->getUpdatedAt());
            $manager->persist($userProfile);

            $user->setUserProfile($userProfile);
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        // Create organizer users
        for ($i = 0; $i < 5; $i++) {
            $organizer = new User();
            $organizer->setEmail($faker->unique()->safeEmail);
            $organizer->setRoles(['ROLE_ORGANIZER']);
            $organizer->setPassword($this->passwordHasher->hashPassword($organizer, 'password'));
            $organizer->setIsVerified(true);
            $organizer->setCreatedAt($faker->dateTimeThisDecade());
            $organizer->setUpdatedAt($faker->dateTimeThisMonth());

            $organizerProfile = new OrganizerProfile();
            $organizerProfile->setUser($organizer);
            $organizerProfile->setCompanyName($faker->company);
            $organizerProfile->setBio($faker->text);
            $organizerProfile->setWebsite($faker->url);
            $organizerProfile->setPhoneNumber($faker->phoneNumber);
            $organizerProfile->setAddress($faker->address);
            $organizerProfile->setCreatedAt($organizer->getCreatedAt());
            $organizerProfile->setUpdatedAt($organizer->getUpdatedAt());
            $manager->persist($organizerProfile);

            $organizer->setOrganizerProfile($organizerProfile);
            $manager->persist($organizer);
            $this->addReference('organizer_' . $i, $organizer);
        }

        $manager->flush();
    }
}
