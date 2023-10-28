<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-30
 * Time: 4:16 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Images;

use Maatify\WebPConverter\WebPConverter;

class UploadImageToWebP extends UploadImage
{

    protected function Upload(): array
    {
        $file = parent::Upload();
        if(!empty($file['uploaded'])){
            if(!empty($this->extension) && $this->extension != 'webp') {
                (new WebPConverter())->WebPConvert($this->file_target);
                if (file_exists((preg_replace('/\\.[^.\\s]{3,4}$/', '', $this->file_target)) . '.webp')) {
                    unlink($this->file_target);
                    $this->file_target = (preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['image'])) . '.webp';

                    return $this->ReturnSuccess($this->file_target);
                } else {
                    return $file;
                }
            }else{
                return $file;
            }
        }else{
            return $file;
        }
    }


}