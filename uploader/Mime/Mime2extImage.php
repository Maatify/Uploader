<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-30
 * Time: 5:36 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Mime;

abstract class Mime2extImage extends MimeValidate
{
    protected function mime2extImage($mime): string
    {
        $all_mimes = [
            'png'	=>	array('image/png',  'image/x-png'),
            'bmp'	=>	array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
            'gif'	=>	'image/gif',
            'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
            'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
        ];
        return $this->MimeValidate($all_mimes, $mime);
    }
}