<?php

namespace App\Tests\Api\Booking;

use App\Entity\User;
use App\Service\Booking\BookingService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Kernel;

class BookingControllerTest extends WebTestCase
{
    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    public function testBookTicket(): void
    {
        $client = static::createClient();
        $user = new User();
        $client->loginUser($user);

        $bookingService = $this->createMock(BookingService::class);
        static::getContainer()->set('App\Service\Booking\BookingService', $bookingService);

        $client->request('POST', '/api/booking/ticket', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'ticket_type_id' => 1,
            'quantity' => 1,
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
