<?php

namespace App\Service\Booking;

use App\Entity\Booking;
use App\Entity\Event;
use App\Entity\TicketType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BookingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function create(User $user, array $data): Booking
    {
        $eventId = $data['event_id'] ?? null;
        $ticketTypeId = $data['ticket_type_id'] ?? null;
        $quantity = (int) ($data['quantity'] ?? 1);

        if (!$eventId || !$ticketTypeId || $quantity < 1) {
            throw new BadRequestHttpException("Missing or invalid booking details: event_id, ticket_type_id, or quantity.");
        }

        $event = $this->em->getRepository(Event::class)->find($eventId);
        if (!$event) {
            throw new BadRequestHttpException("The requested event does not exist.");
        }

        
        $ticketType = $this->em->getRepository(TicketType::class)->find($ticketTypeId);
        if (!$ticketType) {
            throw new BadRequestHttpException("The selected ticket type does not exist.");
        }

        // Ensure ticket belongs to event
        if ($ticketType->getEvent()->getId() !== $event->getId()) {
            throw new BadRequestHttpException("This ticket type does not belong to the selected event.");
        }

        // Prevent self-booking
        if ($event->getCreatedBy()->getId() === $user->getId()) {
            throw new BadRequestHttpException("You cannot book your own event.");
        }

        $now = new \DateTime();

        // Validate availability window
        if ($now < $ticketType->getAvailableFrom()) {
            throw new BadRequestHttpException("Ticket sales haven't started yet.");
        }

        if ($now > $ticketType->getAvailableTo()) {
            throw new BadRequestHttpException("Ticket sales for this type have ended.");
        }

        // Check available quantity
        if ($ticketType->getQuantity() < $quantity) {
            throw new BadRequestHttpException("Insufficient ticket quantity available.");
        }

        // All good — proceed with booking
        $booking = new Booking();
        $booking->setUser($user);
        $booking->setEvent($event);
        $booking->setTicketType($ticketType);
        $booking->setQuantity($quantity);
        $booking->setBookedAt(new \DateTime());

        // Update ticket stock
        $ticketType->setQuantity($ticketType->getQuantity() - $quantity);

        $this->em->persist($booking);
        $this->em->flush();

        return $booking;
    }
}
