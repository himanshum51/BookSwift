<?php

namespace App\DTO\Review;

class ReviewResponseDTO
{
    public int $id;
    public int $rating;
    public ?string $comment;
    public string $userName;
    public string $createdAt;
}
