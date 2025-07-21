<?php

namespace App\Controller\Api\Booking;

use App\Service\Booking\OrganizerBookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api/organizer/bookings', name: 'api_organizer_bookings_')]
class OrganizerBookingController extends AbstractController
{
    public function __construct(private readonly OrganizerBookingService $bookingService) {}

    #[Route('/{eventId}', name: 'event_bookings', methods: ['GET'])]
    public function eventBookings(int $eventId): JsonResponse
    {
        $organizer = $this->getUser();

        if (!in_array('ROLE_ORGANIZER', $organizer->getRoles())) {
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }

        $data = $this->bookingService->getBookingsForEvent($organizer, $eventId);

        if (isset($data['error'])) {
            return $this->json(['success' => false, 'message' => $data['error']], JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
