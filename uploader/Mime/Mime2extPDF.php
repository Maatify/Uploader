<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-30
 * Time: 5:36 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Mime;

abstract class Mime2extPDF extends Mime2extImage
{
    protected function mime2extPDF($mime): string
    {
        $all_mimes = [
            'pdf'	=>	array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'),
        ];
        return $this->MimeValidate($all_mimes, $mime);
    }
}