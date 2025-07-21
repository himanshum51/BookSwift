<?php

namespace App\MessageHandler;

use App\Message\SendBookingTicketMessage;
use App\Repository\BookingRepository;
use App\Service\Pdf\PdfGeneratorService;
use App\Service\EmailService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendBookingTicketHandler
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
        private readonly PdfGeneratorService $pdfGeneratorService,
        private readonly EmailService $emailService,
    ) {}

    public function __invoke(SendBookingTicketMessage $message): void
    {
        $booking = $this->bookingRepository->find($message->getBookingId());

        if (!$booking) {
            return; // Optionally log error
        }

        $pdfContent = $this->pdfGeneratorService->generateTicketPdf($booking);

        $this->emailService->sendTicketWithPdf($booking->getUser(), $booking->getEvent(), $pdfContent);
    }
}
