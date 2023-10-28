<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-10-27
 * Time: 8:36 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Pdf;

use Maatify\Uploader\UploadBase;

class UploadPDF extends UploadBase
{
    protected function allowedExtensions(): array
    {
        return ['pdf'];
    }

    protected function validateMime(string $mime): string
    {
        return $this->mime2extPDF($mime);
    }
}
