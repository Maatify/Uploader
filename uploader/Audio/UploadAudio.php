<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-10-29
 * Time: 9:39 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Audio;

use Maatify\Uploader\UploadBase;

class UploadAudio extends UploadBase
{
    protected function allowedExtensions(): array
    {
        return ['mp3', 'wav', 'aac', 'ogg', 'flac', 'm4a', 'wma', 'opus', 'aiff', 'amr', '3gp'];
    }

    protected function validateMime(string $mime): string
    {
        return $this->mime2extVideo($mime);
    }
}