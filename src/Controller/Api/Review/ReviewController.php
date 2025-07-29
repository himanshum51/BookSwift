<?php
namespace App\Controller\Api\Review;

use App\DTO\Review\ReviewRequestDTO;
use App\Mapper\ReviewMapper;
use App\Service\Review\ReviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/events/{eventId}/reviews')]
class ReviewController extends AbstractController
{
    public function __construct(
        private ReviewService $service,
        private ReviewMapper $mapper
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(int $eventId, Request $request, ValidatorInterface $validator)
    {
        $dto = new ReviewRequestDTO();
        $data = json_decode($request->getContent(), true);

        $dto->rating = $data['rating'] ?? 0;
        $dto->comment = $data['comment'] ?? null;

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => 'Validation failed', 'details' => (string) $errors], 422);
        }

        $review = $this->service->createReview($eventId, $dto);
        $responseDto = $this->mapper->toResponseDTO($review);
        return new JsonResponse(['data' => $responseDto, 'message' => 'Review added successfully'], 201);
    }

    #[Route('', methods: ['GET'])]
    public function list(int $eventId)
    {
        $reviews = $this->service->listReviews($eventId);
        return $this->json(['data' => $reviews]);
    }

    #[Route('/{reviewId}', methods: ['PUT'])]
    public function update(int $reviewId, Request $request, ValidatorInterface $validator)
    {
        $dto = new ReviewRequestDTO();
        $data = json_decode($request->getContent(), true);

        $dto->rating = $data['rating'] ?? 0;
        $dto->comment = $data['comment'] ?? null;

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => 'Validation failed', 'details' => (string) $errors], 422);
        }

        $review = $this->service->updateReview($reviewId, $dto);
        $responseDto = $this->mapper->toResponseDTO($review);
        return new JsonResponse(['data' => $responseDto, 'message' => 'Review updated successfully']);
    }

    #[Route('/{reviewId}', methods: ['DELETE'])]
    public function delete(int $reviewId)
    {
        $this->service->deleteReview($reviewId);
        return new JsonResponse(null, 204);
    }

    #[Route('/rating', methods: ['GET'])]
    public function getAverageRating(int $eventId)
    {
        $averageRating = $this->service->getAverageRating($eventId);
        return $this->json(['averageRating' => $averageRating]);
    }
}
