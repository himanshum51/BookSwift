<?php

namespace App\Controller\Api\Event;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/user/events')]
class EventViewController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('', name: 'event_list_public', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $events = $this->em->getRepository(Event::class)->findBy([
            'isDeleted' => false,
            'status' => 'published',
        ]);

        $response = [];

        foreach ($events as $event) {
            $response[] = [
                'id' => $event->getId(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'location' => $event->getLocation(),
                'start_date' => $event->getStartDate()->format('Y-m-d H:i:s'),
                'end_date' => $event->getEndDate()->format('Y-m-d H:i:s'),
                'banner' => $event->getBanner(),
            ];
        }

        return $this->json($response);
    }

    #[Route('/{id}', name: 'event_detail_public', methods: ['GET'])]
    public function detail(int $id): JsonResponse
    {
        $event = $this->em->getRepository(Event::class)->find($id);

        if (!$event || $event->getStatus() !== 'published') {
            return $this->json(['error' => 'Event not found or not available'], JsonResponse::HTTP_NOT_FOUND);
        }

        $user = $this->getUser();

        $canBook = false;
        if ($user && $user->getId() !== $event->getOrganizer()->getId()) {
            $canBook = true;
        }

        return $this->json([
            'id' => $event->getId(),
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'location' => $event->getLocation(),
            'start_date' => $event->getStartDate()->format('Y-m-d H:i:s'),
            'end_date' => $event->getEndDate()->format('Y-m-d H:i:s'),
            'banner' => $event->getBanner(),
            'total_bookings' => $event->getTotalBookings(),
            'can_book' => $canBook,
            'ticket_types' => array_map(fn($ticket) => [
                'id' => $ticket->getId(),
                'name' => $ticket->getName(),
                'price' => $ticket->getPrice(),
                'quantity' => $ticket->getQuantity(),
                'available_from' => $ticket->getAvailableFrom()->format('Y-m-d H:i:s'),
                'available_to' => $ticket->getAvailableTo()->format('Y-m-d H:i:s'),
            ], $event->getTicketTypes()->toArray()),
        ]);
    }
}
