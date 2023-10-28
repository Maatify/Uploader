<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-30
 * Time: 5:38 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Mime;

use Maatify\Uploader\UploadFolderCreate;

abstract class MimeValidate extends UploadFolderCreate
{
    protected int $max_width = 0;
    protected int $max_height = 0;
    protected int $max_size = 0;

    /**
     * Set the maximum allowed width for an uploaded image.
     */
    protected function MaxWidth(int $width): self
    {
        $this->max_width = $width;
        return $this;
    }

    /**
     * Set the maximum allowed height for an uploaded image.
     */
    protected function MaxHeight(int $height): self
    {
        $this->max_height = $height;
        return $this;
    }

    /**
     * Set the maximum allowed size for an uploaded file in MB.
     * Converts the size to bytes for internal validation.
     */
    protected function MaxSize(int $max_size): self
    {
        $this->max_size = $max_size * 1024 * 1024; // Convert MB to bytes
        return $this;
    }

    /**
     * Validates the MIME type against a list of allowed MIME types.
     * Returns the corresponding file extension if the MIME type is valid.
     *
     * @param array $all_mimes List of valid MIME types mapped to their extensions.
     * @param string $mime_type The MIME type of the uploaded file.
     * @return string The file extension corresponding to the MIME type, or an empty string if not valid.
     */
    protected function MimeValidate(array $all_mimes, string $mime_type): string
    {
        // Check if the MIME type directly matches any of the keys (extensions).
        if (($key = array_search($mime_type, $all_mimes, true)) !== false) {
            return $key;
        }

        // Loop through the array to find the MIME type in the sub-array of MIME types.
        foreach ($all_mimes as $key => $mimes) {
            if (is_array($mimes) && in_array($mime_type, $mimes, true)) {
                return $key;
            }
        }

        // Return an empty string if no matching MIME type is found.
        return '';
    }

    /**
     * Generates an error response for a failed file upload.
     *
     * @param string $description A message describing the error.
     * @return array The structured error response.
     */
    protected function ReturnError(string $description): array
    {
        return ['uploaded' => 0, 'description' => $description];
    }

    /**
     * Generates a success response for a successful file upload.
     *
     * @param string $file The name or path of the successfully uploaded file.
     * @return array The structured success response.
     */
    protected function ReturnSuccess(string $file): array
    {
        return ['uploaded' => 1, 'file' => $file];
    }

    protected function mime2extImage($mime): string
    {
        $all_mimes = [
            'png'  => array('image/png', 'image/x-png'),
            'bmp'  => array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
            'gif'  => 'image/gif',
            'jpeg' => array('image/jpeg', 'image/pjpeg'),
            'jpg'  => array('image/jpeg', 'image/pjpeg'),
            'webp' => array('image/webp'),
        ];

        return $this->MimeValidate($all_mimes, $mime);
    }

    protected function mime2extPDF($mime): string
    {
        $all_mimes = [
            'pdf'	=>	array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'),
        ];
        return $this->MimeValidate($all_mimes, $mime);
    }

    protected function mime2extVideo($mime): string
    {
        $all_mimes = [
            'mp4'  => ['video/mp4', 'application/mp4'],
            'webm' => ['video/webm'],
            'avi'  => ['video/x-msvideo', 'video/avi', 'application/x-troff-msvideo'],
            'mov'  => ['video/quicktime'],
            'mkv'  => ['video/x-matroska', 'video/mkv'],
            'flv'  => ['video/x-flv'],
            'wmv'  => ['video/x-ms-wmv'],
            '3gp'  => ['video/3gpp', 'audio/3gpp'],
            'mpeg' => ['video/mpeg', 'video/x-mpeg'],
            'ogg'  => ['video/ogg'],
            'm4v'  => ['video/x-m4v'],
        ];

        return $this->MimeValidate($all_mimes, $mime);
    }
}
