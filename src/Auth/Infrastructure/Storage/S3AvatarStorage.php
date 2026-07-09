<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Storage;

use App\Auth\Domain\Model\UserId;
use App\Auth\Domain\Service\AvatarStorage;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class S3AvatarStorage implements AvatarStorage
{
    private const string PREFIX = 'avatars/';

    public function __construct(
        private S3Client $client,
        #[Autowire('%env(AWS_S3_BUCKET)%')]
        private string $bucket,
        #[Autowire('%env(AWS_S3_PUBLIC_ENDPOINT)%')]
        private string $publicEndpoint = '',
    ) {
    }

    public function store(UserId $userId, string $content, string $extension): string
    {
        $extension = preg_replace('/[^a-z0-9]/', '', strtolower($extension));

        if ('' === $extension) {
            throw new \InvalidArgumentException('Invalid avatar file extension.');
        }

        $this->deleteExisting($userId);

        $filename = $userId->toString().'.'.$extension;

        $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => self::PREFIX.$filename,
            'Body' => $content,
            'ContentType' => 'jpg' === $extension ? 'image/jpeg' : 'image/'.$extension,
        ]);

        return $filename;
    }

    public function delete(string $filename): void
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => self::PREFIX.basename($filename),
        ]);
    }

    public function publicUrl(string $filename): string
    {
        $key = self::PREFIX.basename($filename);

        if ('' !== $this->publicEndpoint) {
            return rtrim($this->publicEndpoint, '/').'/'.$this->bucket.'/'.$key;
        }

        return $this->client->getObjectUrl($this->bucket, $key);
    }

    public function exists(string $filename): bool
    {
        return $this->client->doesObjectExist($this->bucket, self::PREFIX.basename($filename));
    }

    private function deleteExisting(UserId $userId): void
    {
        $result = $this->client->listObjectsV2([
            'Bucket' => $this->bucket,
            'Prefix' => self::PREFIX.$userId->toString().'.',
        ]);

        foreach ($result['Contents'] ?? [] as $object) {
            $this->client->deleteObject(['Bucket' => $this->bucket, 'Key' => $object['Key']]);
        }
    }
}
