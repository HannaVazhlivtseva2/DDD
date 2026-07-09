<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Storage;

use Aws\S3\S3Client;

final class S3ClientFactory
{
    public static function create(string $region, string $accessKey, string $secretKey, string $endpoint): S3Client
    {
        $config = [
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ];

        if ('' !== $endpoint) {
            $config['endpoint'] = $endpoint;
            $config['use_path_style_endpoint'] = true;
        }

        return new S3Client($config);
    }
}
