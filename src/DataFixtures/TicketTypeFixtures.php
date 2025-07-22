<?php

namespace App\DataFixtures;

use App\Entity\TicketType;
use App\DataFixtures\EventFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TicketTypeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $event = $this->getReference('event_' . $i, \App\Entity\Event::class);

            for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {
                $ticketType = new TicketType();
                $ticketType->setEvent($event);
                $ticketType->setName($faker->word . ' Ticket');
                $ticketType->setDescription($faker->sentence);
                $ticketType->setPrice($faker->numberBetween(10, 100));
                $ticketType->setQuantity($faker->numberBetween(50, 200));
                $availableFrom = $faker->dateTimeBetween('-1 month', $event->getStartDate()->format('Y-m-d H:i:s'));
                $ticketType->setAvailableFrom($availableFrom);
                $ticketType->setAvailableTo($event->getStartDate());

                $manager->persist($ticketType);
                $this->addReference('ticket_type_' . $i . '_' . $j, $ticketType);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixtures::class,
        ];
    }
}
