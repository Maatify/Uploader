<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-07-04
 * Time: 8:36 PM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Images;

use Maatify\Uploader\UploadBase;

class UploadImage extends UploadBase
{
    /**
     * @return array<int, string>
     */
    protected function allowedExtensions(): array
    {
        return ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'];
    }

    /**
     *  @return string
     */
    protected function validateMime(string $mime): string
    {
        return $this->mime2extImage($mime);
    }
}
