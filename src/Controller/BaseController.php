<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractController
{
    protected function success(
        string $message,
        mixed $data = null,
        int $statusCode = 200,
        ?array $meta = null
    ): JsonResponse {
        return new JsonResponse([
            'status' => 'success',
            'code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'meta' => $meta ?? [],
        ], $statusCode);
    }

    protected function error(
        string $message,
        mixed $errors = null,
        int $statusCode = 400,
        ?array $meta = null
    ): JsonResponse {
        return new JsonResponse([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $message,
            'errors' => $errors,
            'meta' => $meta ?? [],
        ], $statusCode);
    }
}
