<?php

namespace App\Scheduler\Service;

use App\Repository\EventRepository;
use App\Repository\OrganizerProfileRepository;
use App\Service\EmailService;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EventSummaryMailer
{
    public function __construct(
        private EventRepository $eventRepository,
        private OrganizerProfileRepository $organizerRepo,
        private EmailService $emailService,
        private Environment $twig
    ) {}

    public function sendSummaryToAllOrganizers(): void
    {
        $organizers = $this->organizerRepo->findAll();

        foreach ($organizers as $organizer) {
            $events = $this->eventRepository->findTodayEventsByOrganizer($organizer);

            if (count($events) === 0) {
                continue;
            }

            $html = $this->twig->render('emails/daily_summary.html.twig', [
                'organizer' => $organizer,
                'events' => $events,
            ]);

            $this->emailService->sendEmail(
                $organizer->getUser()->getEmail(),
                'Your Daily Event Summary',
                $html
            );
        }
    }
}
