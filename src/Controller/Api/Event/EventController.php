<?php

namespace App\Controller\Api\Event;

use App\Entity\Event;
use App\Service\Event\EventService;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/event')]
#[IsGranted('ROLE_ORGANIZER')]
class EventController extends AbstractController
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly FileUploader $fileUploader,
    ) {}

    #[Route('', name: 'event_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $event = $this->eventService->create($request, $this->getUser());
        return $this->json(['message' => 'Event created!', 'id' => $event->getId()]);
    }

    #[Route('', name: 'event_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $events = $this->eventService->getForUser($this->getUser());
        return $this->json($events);
    }

    #[Route('/{id}', name: 'event_get', methods: ['GET'])]
    public function get(Event $event): JsonResponse
    {
        if ($event->getCreatedBy() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Unauthorized');
        }

        $ticketTypes = $event->getTicketTypes()->map(function ($ticket) {
            return [
                'id' => $ticket->getId(),
                'name' => $ticket->getName(),
                'description' => $ticket->getDescription(),
                'price' => $ticket->getPrice(),
                'quantity' => $ticket->getQuantity(),
                'available_from' => $ticket->getAvailableFrom()->format('Y-m-d H:i'),
                'available_to' => $ticket->getAvailableTo()->format('Y-m-d H:i'),
            ];
        })->toArray();

        return $this->json([
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'startDate' => $event->getStartDate()->format('Y-m-d H:i'),
            'endDate' => $event->getEndDate()->format('Y-m-d H:i'),
            'location' => $event->getLocation(),
            'status' => $event->getStatus(),
            'totalBookings' => $event->getTotalBookings(),
            'banner' => $event->getBanner() ? $this->fileUploader->getPublicUrl($event->getBanner()) : null,
            'ticketTypes' => $ticketTypes,
        ]);
    }

    #[Route('/{id}', name: 'event_update', methods: ['PUT', 'PATCH','POST'])]
    public function update(Request $request, Event $event): JsonResponse
    {
        $this->eventService->update($event, $request, $this->getUser());
        return $this->json(['message' => 'Event updated!']);
    }

    #[Route('/{id}', name: 'event_delete', methods: ['DELETE'])]
    public function delete(Event $event): JsonResponse
    {
        $this->eventService->softDelete($event, $this->getUser());
        return $this->json(['message' => 'Event deleted!']);
    }
}
