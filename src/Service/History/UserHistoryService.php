<?php

namespace App\Service\History;

use App\Repository\BookingRepository;

class UserHistoryService
{
    public function __construct(
        private readonly BookingRepository $bookingRepository,
    ) {}

    public function getUserHistory(int $userId): array
    {
        return $this->bookingRepository->findPastBookingsForUser($userId);
    }
}
