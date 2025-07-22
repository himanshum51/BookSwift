<?php

namespace App\DataFixtures;

use App\Entity\Event;
use App\DataFixtures\UserFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $event = new Event();
            $event->setTitle($faker->sentence(4));
            $event->setDescription($faker->text);
            $startDate = $faker->dateTimeBetween('+1 week', '+1 month');
            $endDate = (clone $startDate)->modify('+'. $faker->numberBetween(1, 5) . ' hours');
            $event->setStartDate($startDate);
            $event->setEndDate($endDate);
            $event->setLocation($faker->address);
            $event->setBanner($faker->imageUrl(640, 480, 'events', true));
            $event->setStatus($faker->randomElement(['draft', 'published', 'archived']));
            $event->setIsDeleted(false);
            $event->setCreatedBy($this->getReference('organizer_' . $faker->numberBetween(0, 4), \App\Entity\User::class));
            $event->setTotalBookings(0);

            $manager->persist($event);
            $this->addReference('event_' . $i, $event);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
