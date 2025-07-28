<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\OrganizerProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Find active (non-deleted) events created by a specific user.
     */
    public function findByOrganizer(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.createdBy = :user')
            ->andWhere('e.isDeleted = false')
            ->setParameter('user', $user)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find all active published events (for future user listing, etc).
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->andWhere('e.isDeleted = false')
            ->setParameter('status', 'published')
            ->orderBy('e.startDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTodayEventsByOrganizer(OrganizerProfile $organizer): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.createdBy', 'u')
            ->leftJoin('u.organizerProfile', 'o')
            ->where('o.id = :organizerId')
            ->andWhere('e.startDate >= :startOfDay')
            ->andWhere('e.startDate < :endOfDay')
            ->setParameter('organizerId', $organizer->getId())
            ->setParameter('startOfDay', new \DateTime('today'))
            ->setParameter('endOfDay', new \DateTime('tomorrow'))
            ->groupBy('e.id');

        return $qb->getQuery()->getResult();
    }
}
