<?php

namespace App\Controller\Api\Booking;

use App\Service\Booking\BookingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/api/booking')]
class BookingController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BookingService $bookingService,
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {}

    #[Route('', name: 'booking_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $data = json_decode($request->getContent(), true);
            $booking = $this->bookingService->create($user, $data);

            return $this->json([
                'message' => 'Booking successful',
                'booking_id' => $booking->getId(),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
