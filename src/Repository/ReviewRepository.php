<?php
namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Find reviews by event ID (ordered by newest first)
     */
    public function findByEvent(int $eventId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.event = :eventId')
            ->setParameter('eventId', $eventId)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get average rating for a specific event
     */
    public function getAverageRating(int $eventId): ?float
    {
        return $this->createQueryBuilder('r')
            ->select('AVG(r.rating)')
            ->andWhere('r.event = :eventId')
            ->setParameter('eventId', $eventId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Check if a user has already reviewed an event
     */
    public function findUserReview(int $eventId, int $userId): ?Review
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.event = :eventId')
            ->andWhere('r.user = :userId')
            ->setParameters([
                'eventId' => $eventId,
                'userId'  => $userId
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
