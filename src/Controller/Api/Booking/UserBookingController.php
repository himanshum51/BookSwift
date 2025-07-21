<?php

namespace App\Controller\Api\Booking;

use App\Service\Booking\UserBookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user/bookings', name: 'api_user_bookings_')]
class UserBookingController extends AbstractController
{
    public function __construct(private readonly UserBookingService $userBookingService) {}

    #[Route('/', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {

        $user = $this->getUser();
        if (!in_array('ROLE_USER', $user->getRoles())) {
            return $this->json(['error' => 'Access denied'], JsonResponse::HTTP_FORBIDDEN);
        }
        $bookings = $this->userBookingService->getUserBookings($user);

        return $this->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }
}
