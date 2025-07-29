<?php
namespace App\Mapper;

use App\DTO\Review\ReviewResponseDTO;
use App\Entity\Review;

class ReviewMapper
{
    public function toResponseDTO(Review $review): ReviewResponseDTO
    {
        $dto = new ReviewResponseDTO();
        $dto->id = $review->getId();    
        $dto->rating = $review->getRating();
        $dto->comment = $review->getComment();
        $userProfile = $review->getUser()->getUserProfile();
        $dto->userName = $userProfile ? $userProfile->getFullName() : $review->getUser()->getEmail();
        $dto->createdAt = $review->getCreatedAt()->format('Y-m-d H:i:s');

        return $dto;
    }
}
