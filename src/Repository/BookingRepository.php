<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    public function findPastBookingsForUser(int $userId): array
    {
        return $this->createQueryBuilder('b')
            ->join('b.ticketType', 'tt')
            ->join('tt.event', 'e')
            ->where('b.user = :user')
            ->andWhere('e.endDate < :now')  
            ->setParameter('user', $userId)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.endDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}