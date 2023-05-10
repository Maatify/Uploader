<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-21
 * Time: 12:59 PM
 */

namespace Maatify\Uploader\Base64;

use Maatify\Json\Json;
use Maatify\Uploader\Mime\Mime2extPDF;

abstract class UploadBase64 extends Mime2extPDF
{
    protected int $uploaded_for_id;
    protected string $upload_folder;
    protected string $file_dir;
    protected string $file_name;

    protected int $max_width = 0;
    protected int $max_height = 0;
    protected int $max_size = 0;

    protected function Upload(): string
    {
        if (empty($_POST['base64_file'])) {
            Json::Missing('base64_file');
        }
        if (is_array($_POST['base64_file'])) {
            Json::Invalid('base64_file');
        }

        $_POST['base64_file'] = preg_replace('#^data:image/\w+;base64,#i', '', $_POST['base64_file']);
        $_POST['base64_file'] = preg_replace('#^data:application/pdf;base64,#i', '', $_POST['base64_file']);
        $decoded_file = base64_decode($_POST['base64_file']); // decode the file
        $mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE); // extract mime type
        $extension = $this->mime2extImage($mime_type); // extract extension from mime type
        if(empty($extension)){
            $extension = $this->mime2extPDF($mime_type); // extract extension from mime type
        }
        if (!empty($extension)) {
            $size = getimagesizefromstring($decoded_file);
            if(!empty($this->max_width) && $size[0] > $this->max_width){
                Json::Incorrect('max_width', 'cannot more than ' . $this->max_width);
            }
            if(!empty($this->max_height) && $size[1] > $this->max_height){
                Json::Incorrect('max_height', 'cannot more than ' . $this->max_height);
            }
            if(!empty($this->max_size) && /*filesize($decoded_file) */strlen($decoded_file) > $this->max_size*1024){
                Json::Incorrect('max_size', 'cannot more than ' . $this->max_size . ' MB');
            }
            if(empty($this->file_name)){
                $fileName = round(microtime(true) * 1000) . uniqid();
                $file = $this->uploaded_for_id . '_' . time() . "_" . $fileName . uniqid() . '.' . $extension;
            }else{
                $file = $this->file_name . '.' . $extension;
            }

            $this->file_dir = $this->upload_folder . '/' . $file;
            try {
                if (file_put_contents($this->file_dir, $decoded_file)) { // save
                    return $file;
                } else {
                    Json::Invalid('base64_file', 'Cannot upload file ' . __LINE__);
                }
            } catch (\Exception $e) {
                Json::Invalid('base64_file', $e->getMessage() . ' ' . __LINE__);
            }
        } else {
            Json::NotAllowedToUse('base64_file', 'Invalid base64_file This file not allowed to Upload');
        }
        return '';
    }

//    protected function mime2extImage($mime): string
//    {
//        $all_mimes = [
//            'png'	=>	array('image/png',  'image/x-png'),
//            'bmp'	=>	array('image/bmp', 'image/x-bmp', 'image/x-bitmap', 'image/x-xbitmap', 'image/x-win-bitmap', 'image/x-windows-bmp', 'image/ms-bmp', 'image/x-ms-bmp', 'application/bmp', 'application/x-bmp', 'application/x-win-bitmap'),
//            'gif'	=>	'image/gif',
//            'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
//            'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
//        ];
//        return $this->MimeValidate($all_mimes, $mime);
//    }
//
//    protected function mime2extPDF($mime): string
//    {
//        $all_mimes = [
//            'pdf'	=>	array('application/pdf', 'application/force-download', 'application/x-download', 'binary/octet-stream'),
//        ];
//        return $this->MimeValidate($all_mimes, $mime);
//    }
//
//    protected function MimeValidate(array $all_mimes, string $mime_type): string{
//        if (($key = array_search($mime_type, $all_mimes, TRUE))) {
//            return $key;
//        }
//
//        foreach ($all_mimes as $key => $mimes) {
//            if (is_array($mimes) && in_array($mime_type, $mimes)) {
//                return $key;
//            }
//        }
//
//        return '';
//    }
}