<?php

namespace App\Service\Pdf;

use App\Entity\Booking;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class PdfGeneratorService
{
    public function __construct(private readonly Environment $twig) {}

    public function generateTicketPdf(Booking $booking): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        $html = $this->twig->render('pdf/ticket.html.twig', [
            'booking' => $booking,
            'event' => $booking->getEvent(),
            'user' => $booking->getUser(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output(); 
    }
}
