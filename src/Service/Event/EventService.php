<?php
namespace App\Service\Event;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Event\EventUpdatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FileUploader $fileUploader,
        private readonly EventRepository $eventRepository,
        private readonly EventDispatcherInterface $dispatcher,
    ) {}

    public function create(Request $request, User $user): Event
    {
        $event = new Event();
        $this->mapFields($event, $request);
        $event->setCreatedBy($user);
        $this->em->persist($event);
        $this->em->flush();
        return $event;
    }

    public function update(Event $event, Request $request, User $user): void
    {
        if ($event->getCreatedBy() !== $user) {
            throw new \Exception('Unauthorized');
        }

        $this->mapFields($event, $request);
        $this->em->flush();

        // Dispatch event after updating
        $this->dispatcher->dispatch(new EventUpdatedEvent($event));
    }

    public function softDelete(Event $event, User $user): void
    {
        if ($event->getCreatedBy() !== $user) {
            throw new \Exception('Unauthorized');
        }

        $event->setIsDeleted(true);
        $this->em->flush();
    }

    public function getForUser(User $user): array
    {
        $events = $this->eventRepository->findBy([
            'createdBy' => $user,
            'isDeleted' => false,
        ]);

        return array_map(fn(Event $e) => [
            'id' => $e->getId(),
            'title' => $e->getTitle(),
            'startDate' => $e->getStartDate()->format('Y-m-d H:i'),
            'endDate' => $e->getEndDate()->format('Y-m-d H:i'),
            'status' => $e->getStatus(),
            'totalBookings' => $e->getTotalBookings(),
            'banner' => $e->getBanner() ? $this->fileUploader->getPublicUrl($e->getBanner()) : null,
        ], $events);
    }

    private function mapFields(Event $event, Request $request): void
    {
        $data = $request->request;

        if ($data->get('title')) $event->setTitle($data->get('title'));
        if ($data->get('description')) $event->setDescription($data->get('description'));
        if ($data->get('startDate')) $event->setStartDate(new \DateTime($data->get('startDate')));
        if ($data->get('endDate')) $event->setEndDate(new \DateTime($data->get('endDate')));
        if ($data->get('location')) $event->setLocation($data->get('location'));
        if ($data->get('status')) $event->setStatus($data->get('status'));
        if ($data->get('totalBookings')) $event->setTotalBookings((int)$data->get('totalBookings'));
        

        if ($request->files->get('banner')) {
            $path = $this->fileUploader->uploadProfilePhoto($request->files->get('banner'));
            $event->setBanner($path);
        }

        $event->setUpdatedAt(new \DateTimeImmutable());
    }
}
