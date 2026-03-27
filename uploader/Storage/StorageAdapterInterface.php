<?php

/**
 * @copyright   ©2026 Maatify.dev
 * @Library     maatify/Uploader
 * @Project     maatify:Uploader
 * @author      Mohamed Abdulalim (megyptm) <mohamed@maatify.dev>
 * @since       2026-03-27 14:51
 * @see         https://www.maatify.dev Maatify.dev
 * @link        https://github.com/Maatify/Uploader view Project on GitHub
 * @note        Distributed in the hope that it will be useful - WITHOUT WARRANTY.
 */

declare(strict_types=1);

namespace Maatify\Uploader\Storage;

interface StorageAdapterInterface
{
    /**
     * Upload a file to storage
     *
     * @param string $localPath  Temporary local file path
     * @param string $remotePath Destination path on the cloud storage
     *
     * @return string Final URL or relative file path
     */
    public function upload(string $localPath, string $remotePath): string;

    public function delete(string $remotePath): void;

    public function exists(string $remotePath): bool;
}
