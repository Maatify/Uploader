<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-21
 * Time: 12:59 PM
 */

namespace Maatify\Uploader\Base64;

use Maatify\WebPConverter\WebPConverter;

abstract class UploadBase64ToWebP extends UploadBase64
{
    protected function Upload(): string
    {
        if($file = parent::Upload()){
//            $mime_type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->file_dir);
//            $extension = $this->mime2extImage($mime_type);
            if(!empty($this->extension)){

                (new WebPConverter())->WebPConvert($this->file_dir);
//                $check = getimagesize((preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->file_dir)) . '.webp');
                if(file_exists((preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->file_dir)) . '.webp')){
                    unlink($this->file_dir);
                    return (preg_replace('/\\.[^.\\s]{3,4}$/', '', $file)) . '.webp';
                }else{
                    return $file;
                }
            }else{
                return $file;
            }
        }else{
            return '';
        }
    }

}