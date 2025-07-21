<?php

namespace App\Service\Booking;

use App\Entity\User;
use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;

class OrganizerBookingService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getBookingsForEvent(User $organizer, int $eventId): array
    {
        // Find event created by this organizer
        $event = $this->em->getRepository(Event::class)->findOneBy([
            'id' => $eventId,
            'createdBy' => $organizer,
        ]);

        if (!$event) {
            return ['error' => 'Event not found or access denied'];
        }

        $bookingsData = [];

        foreach ($event->getTicketTypes() as $ticketType) {
            foreach ($ticketType->getBookings() as $booking) {
                $user = $booking->getUser();
                $userProfile = $user?->getUserProfile();
                $fullName = $userProfile 
                    ? trim($userProfile->getFirstName() . ' ' . $userProfile->getLastName()) 
                    : $user->getEmail(); 

                $bookingsData[] = [
                    'booking_id'  => $booking->getId(),
                    'user_name'   => $fullName,
                    'ticket_type' => $ticketType->getName(),
                    'quantity'    => $booking->getQuantity(),
                    'booked_at'   => $booking->getBookedAt()?->format('Y-m-d H:i'),
                ];
            }
        }

        return [
            'event_id'    => $event->getId(),
            'event_title' => $event->getTitle(),
            'bookings'    => $bookingsData,
        ];
    }
}
