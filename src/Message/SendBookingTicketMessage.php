<?php

namespace App\Message;

class SendBookingTicketMessage
{
    public function __construct(
        private readonly int $bookingId
    ) {}

    public function getBookingId(): int
    {
        return $this->bookingId;
    }
}
