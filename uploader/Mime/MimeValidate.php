<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-30
 * Time: 5:38 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Mime;

abstract class MimeValidate
{
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