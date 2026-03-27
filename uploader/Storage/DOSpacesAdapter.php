<?php

/**
 * @copyright   ©2026 Maatify.dev
 * @Library     maatify/Uploader
 * @Project     maatify:Uploader
 * @author      Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since       2026-03-27 14:52
 * @see         https://www.maatify.dev Maatify.dev
 * @link        https://github.com/Maatify/Uploader view Project on GitHub
 * @note        Distributed in the hope that it will be useful - WITHOUT WARRANTY.
 */

namespace Maatify\Uploader\Storage;

use Aws\S3\S3Client;

// composer require aws/aws-sdk-php
// Will only be installed if actually needed

class DOSpacesAdapter implements StorageAdapterInterface
{
    /** @var S3Client */
    private object $client;

    public function __construct(
        private readonly string $key,
        private readonly string $secret,
        private readonly string $region,
        private readonly string $bucket,
        private readonly string $endpoint,
        private readonly bool   $publicRead = true,
    ) {
        if (!class_exists(S3Client::class)) {
            throw new \RuntimeException(
                'aws/aws-sdk-php is required for DOSpacesAdapter. Run: composer require aws/aws-sdk-php'
            );
        }

        $this->client = new S3Client([
            'version'     => 'latest',
            'region'      => $this->region,
            'endpoint'    => $this->endpoint,
            'credentials' => [
                'key'    => $this->key,
                'secret' => $this->secret,
            ],
        ]);
    }


    public function upload(string $localPath, string $remotePath): string
    {
        $handle = fopen($localPath, 'rb');
        if ($handle === false) {
            throw new \RuntimeException("Failed to open local file: $localPath");
        }

        try {
            $this->client->putObject([
                'Bucket' => $this->bucket,
                'Key'    => $remotePath,
                'Body'   => $handle,
                'ACL'    => $this->publicRead ? 'public-read' : 'private',
            ]);
        } finally {
            fclose($handle);
        }
        // Return only the relative path
        return $remotePath;
    }

    public function delete(string $remotePath): void
    {
        $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $remotePath,
        ]);
    }

    public function exists(string $remotePath): bool
    {
        return $this->client->doesObjectExist($this->bucket, $remotePath);
    }
}
