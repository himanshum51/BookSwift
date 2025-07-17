<?php

namespace App\Service;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;


class FileUploader
{
    private FilesystemOperator $storage;
    private string $publicEndpoint;

    public function __construct(FilesystemOperator $minioStorage)
    {
        $this->storage = $minioStorage;
        // Example: your MinIO public URL or CDN base URL
        $this->publicEndpoint = $_ENV['MINIO_PUBLIC_URL'];
    }

    public function uploadProfilePhoto(UploadedFile $file): string
    {
        $filename = 'profiles/' . Uuid::v4() . '.' . $file->guessExtension();
        $stream = fopen($file->getRealPath(), 'r+');

        $this->storage->writeStream($filename, $stream);
        fclose($stream);

        return $filename;
    }

    public function getPublicUrl(string $path): string
    {
        return rtrim($this->publicEndpoint, '/') . '/' . ltrim($path, '/');
    }
}
 