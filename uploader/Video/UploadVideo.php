<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-10-27
 * Time: 8:30 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Video;

use Maatify\Uploader\UploadBase;

class UploadVideo extends UploadBase
{

    protected function allowedExtensions(): array
    {
        return ['mp4', 'webm', 'avi', 'mov', 'mkv', 'flv', 'wmv', '3gp', 'mpeg', 'ogg', 'm4v'];
    }

    protected function validateMime(string $mime): string
    {
        return $this->mime2extVideo($mime);
    }
}
