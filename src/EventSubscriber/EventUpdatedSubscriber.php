<?php

namespace App\EventSubscriber;

use App\Event\EventUpdatedEvent;
use App\Repository\BookingRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EventUpdatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
        private readonly MailerInterface $mailer,
        private readonly Environment $twig
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            EventUpdatedEvent::class => 'onEventUpdated',
        ];
    }

    public function onEventUpdated(EventUpdatedEvent $event): void
    {
        $eventEntity = $event->getEvent();
        $bookings = $this->bookingRepository->findBy(['event' => $eventEntity]);

        foreach ($bookings as $booking) {
            $user = $booking->getUser();
            $email = (new Email())
                ->from('no-reply@yourapp.com')
                ->to($user->getEmail())
                ->subject('Event Updated: ' . $eventEntity->getTitle())
                ->html($this->twig->render('emails/event_updated.html.twig', [
                    'user' => $user,
                    'event' => $eventEntity,
                ]));

            $this->mailer->send($email);
        }
    }
}
