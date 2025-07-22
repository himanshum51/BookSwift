<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\TicketTypeFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $booking = new Booking();
            $booking->setUser($this->getReference('user_' . $faker->numberBetween(0, 9), \App\Entity\User::class));
            
            $eventRef = $faker->numberBetween(0, 19);
            $ticketTypeRef = $faker->numberBetween(0, 4);
            
            // Ensure the ticket type reference exists
            while (!$this->hasReference('ticket_type_' . $eventRef . '_' . $ticketTypeRef, \App\Entity\TicketType::class)) {
                $eventRef = $faker->numberBetween(0, 19);
                $ticketTypeRef = $faker->numberBetween(0, 4);
            }
            
            $ticketType = $this->getReference('ticket_type_' . $eventRef . '_' . $ticketTypeRef, \App\Entity\TicketType::class);
            
            $booking->setEvent($ticketType->getEvent());
            $booking->setTicketType($ticketType);
            $booking->setQuantity($faker->numberBetween(1, 5));
            $booking->setBookedAt($faker->dateTimeThisYear);

            $manager->persist($booking);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            EventFixtures::class,
            TicketTypeFixtures::class,
        ];
    }
}
