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

    protected function MaxWidth(int $width):self{
        $this->max_width = $width;
        return $this;
    }
    protected function MaxHeight(int $height):self
    {
        $this->max_height = $height;
        return $this;
    }
    protected function MaxSize(int $max_size):self
    {
        $this->max_size = $max_size*1024;
        return $this;
    }

    protected function MimeValidate(array $all_mimes, string $mime_type): string{
        if (($key = array_search($mime_type, $all_mimes, TRUE))) {
            return $key;
        }

        foreach ($all_mimes as $key => $mimes) {
            if (is_array($mimes) && in_array($mime_type, $mimes)) {
                return $key;
            }
        }

        return '';
    }
}