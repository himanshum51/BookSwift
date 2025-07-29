<?php
namespace App\Service\Review;

use App\DTO\Review\ReviewRequestDTO;
use App\Entity\Event;
use App\Entity\Review;
use App\Mapper\ReviewMapper;
use App\Repository\EventRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ReviewService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ReviewRepository $reviewRepository,
        private EventRepository $eventRepository,
        private ReviewMapper $mapper,
        private Security $security
    ) {}

    public function createReview(int $eventId, ReviewRequestDTO $dto): Review
    {
        $event = $this->eventRepository->find($eventId);
        if (!$event) {
            throw new NotFoundHttpException('Event not found.');
        }

        $user = $this->security->getUser();

        // Ensure user has booking (pseudo check, adjust logic)
        if (!$event->hasBookingByUser($user)) {
            throw new AccessDeniedHttpException('You can only review events you attended.');
        }

        // Check if review already exists
        $existing = $this->reviewRepository->findOneBy(['event' => $event, 'user' => $user]);
        if ($existing) {
            throw new AccessDeniedHttpException('You have already reviewed this event.');
        }

        $review = new Review();
        $review->setEvent($event);
        $review->setUser($user);
        $review->setRating($dto->rating);
        $review->setComment($dto->comment);

        $this->em->persist($review);
        $this->em->flush();

        return $review;
    }

    public function listReviews(int $eventId): array
    {
        $reviews = $this->reviewRepository->findBy(['event' => $eventId], ['createdAt' => 'DESC']);
        return array_map([$this->mapper, 'toResponseDTO'], $reviews);
    }

    public function updateReview(int $reviewId, ReviewRequestDTO $dto): Review
    {
        $review = $this->reviewRepository->find($reviewId);
        if (!$review) {
            throw new NotFoundHttpException('Review not found.');
        }

        $user = $this->security->getUser();
        if ($review->getUser() !== $user) {
            throw new AccessDeniedHttpException('You are not allowed to edit this review.');
        }

        $review->setRating($dto->rating);
        $review->setComment($dto->comment);

        $this->em->flush();

        return $review;
    }

    public function deleteReview(int $reviewId): void
    {
        $review = $this->reviewRepository->find($reviewId);
        if (!$review) {
            throw new NotFoundHttpException('Review not found.');
        }

        $user = $this->security->getUser();
        if ($review->getUser() !== $user) {
            throw new AccessDeniedHttpException('You are not allowed to delete this review.');
        }

        $this->em->remove($review);
        $this->em->flush();
    }

    public function getAverageRating(int $eventId): float
    {
        $reviews = $this->reviewRepository->findBy(['event' => $eventId]);
        if (count($reviews) === 0) {
            return 0.0;
        }

        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review->getRating();
        }

        return round($totalRating / count($reviews), 2);
    }
}
