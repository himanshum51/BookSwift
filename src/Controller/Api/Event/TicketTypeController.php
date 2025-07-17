<?php

namespace App\Controller\Api\Event;

use App\Entity\TicketType;
use App\Entity\Event;
use App\Service\TicketType\TicketTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/ticket-type')]
class TicketTypeController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TicketTypeService $ticketTypeService,
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
    ) {}

    #[Route('', name: 'ticket_type_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $event = $this->em->getRepository(Event::class)->find($data['event_id'] ?? 0);
        if (!$event) {
            return $this->json(['error' => 'Invalid event ID.'], Response::HTTP_BAD_REQUEST);
        }

        $ticketType = $this->ticketTypeService->createFromArray($data, $event);

        $errors = $this->validator->validate($ticketType);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($ticketType);
        $this->em->flush();

        return $this->json(['message' => 'Ticket type created successfully']);
    }

   #[Route('/{id}', name: 'ticket_type_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $ticketType = $this->em->getRepository(TicketType::class)->find($id);
        if (!$ticketType) {
            return $this->json(['error' => 'Ticket type not found.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->json(['error' => 'Invalid or missing JSON body.'], Response::HTTP_BAD_REQUEST);
        }

        $ticketType = $this->ticketTypeService->updateFromArray($ticketType, $data);

        $errors = $this->validator->validate($ticketType);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->em->flush();

        return $this->json(['message' => 'Ticket type updated successfully']);
    }

}
