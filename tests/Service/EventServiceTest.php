<?php

namespace App\Tests\Service;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Service\Event\EventService;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class EventServiceTest extends TestCase
{
    public function testCreate(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $fileUploader = $this->createMock(FileUploader::class);
        $eventRepository = $this->createMock(EventRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventService = new EventService($entityManager, $fileUploader, $eventRepository, $eventDispatcher);

        $user = new User();
        $request = new Request([], [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'location' => 'Test Location',
            'startDate' => '2025-12-31 12:00:00',
            'endDate' => '2025-12-31 18:00:00',
            'status' => 'scheduled',
            'totalBookings' => 100,
        ]);

        $event = $eventService->create($request, $user);

        $this->assertInstanceOf(Event::class, $event);
    }

    public function testUpdate(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $fileUploader = $this->createMock(FileUploader::class);
        $eventRepository = $this->createMock(EventRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventService = new EventService($entityManager, $fileUploader, $eventRepository, $eventDispatcher);

        $user = new User();
        $event = new Event();
        $event->setCreatedBy($user);

        $request = new Request([], [
            'title' => 'Updated Event Title',
        ]);

        $eventService->update($event, $request, $user);

        $this->assertEquals('Updated Event Title', $event->getTitle());
    }

    public function testSoftDelete(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $fileUploader = $this->createMock(FileUploader::class);
        $eventRepository = $this->createMock(EventRepository::class);
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $eventService = new EventService($entityManager, $fileUploader, $eventRepository, $eventDispatcher);

        $user = new User();
        $event = new Event();
        $event->setCreatedBy($user);

        $eventService->softDelete($event, $user);

        $this->assertTrue($event->getIsDeleted());
    }
}
