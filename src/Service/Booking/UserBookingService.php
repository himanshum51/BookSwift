<?php

namespace App\Service\Booking;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserBookingService
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function getUserBookings(User $user): array
    {
        $bookings = $this->em->getRepository(\App\Entity\Booking::class)
            ->createQueryBuilder('b')
            ->leftJoin('b.ticketType', 'tt')
            ->leftJoin('tt.event', 'e')
            ->addSelect('tt', 'e')
            ->where('b.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $results = [];

        foreach ($bookings as $booking) {
            $results[] = [
                'booking_id' => $booking->getId(),
                'event_title' => $booking->getTicketType()->getEvent()->getTitle(),
                'event_start_date' => $booking->getTicketType()->getEvent()->getStartDate()->format('Y-m-d H:i'),
                'ticket_type' => $booking->getTicketType()->getName(),
                'quantity' => $booking->getQuantity(),
                'price_per_ticket' => $booking->getTicketType()->getPrice(),
                'total_price' => $booking->getQuantity() * $booking->getTicketType()->getPrice(),
                'location' => $booking->getTicketType()->getEvent()->getLocation(),
            ];
        }

        return $results;
    }
}
