<?php
namespace App\DTO\Review;

use Symfony\Component\Validator\Constraints as Assert;

class ReviewRequestDTO
{
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    public int $rating;

    #[Assert\Length(max: 500)]
    public ?string $comment = null;
}
