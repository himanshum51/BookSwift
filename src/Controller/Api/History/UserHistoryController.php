<?php

namespace App\Controller\Api\History;

use App\Service\History\UserHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/history/user')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class UserHistoryController extends AbstractController
{
    public function __construct(
        private readonly UserHistoryService $historyService,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $history = $this->historyService->getUserHistory($user->getId());

        $data = array_map(function ($booking) {
            $event = $booking->getTicketType()->getEvent();
            return [
                'event' => $event->getTitle(),
                'eventEndDate' => $event->getEndDate(),
                'ticketType' => $booking->getTicketType()->getName(),
                'quantity' => $booking->getQuantity(),
            ];
        }, $history);

        return $this->json($data);
    }
}
