<?php

/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-30
 * Time: 4:16 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Images;

use Maatify\Logger\Logger;
use Maatify\WebPConverter\WebPConverter;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use WebPConvert\Exceptions\InvalidInput\InvalidImageTypeException;

class UploadImageToWebP extends UploadImage
{

    /**
     * @param bool $convert
     *
     * @return array{uploaded: int, file?: string, description?: string}
     */
    public function upload(bool $convert = true): array
    {
        $this->skipStoragePush = true;
        /** @var array{uploaded: int, file?: string, description?: string} $file */
        $file = parent::upload();
        $this->skipStoragePush = false;

        if (!empty($file['uploaded']) && isset($file['file'])) {
            $originalFile = $file['file'];

            if (!empty($this->extension)
                && $this->extension != 'webp'
                && ($convert || ($this->extension != 'gif'))
            ) {
                try {
                    (new WebPConverter())->webPConvert($this->file_target);
                } catch (InvalidImageTypeException|ConversionFailedException $exception) {
                    Logger::RecordLog($exception, 'WebPConverter');
                }

                $webpTarget = preg_replace('/\.[^.\s]{3,4}$/', '', $this->file_target) . '.webp';
                $webpFile   = preg_replace('/\.[^.\s]{3,4}$/', '', $originalFile)   . '.webp';

                if (file_exists($webpTarget)) {
                    unlink($this->file_target);
                    $this->file_target = $webpTarget;
                    $this->pushToStorage($this->file_target, $webpFile);
                    return $this->returnSuccess($webpFile);
                }
            }

            // If no conversion is required, proceed with uploading the original file
            $this->pushToStorage($this->file_target, $originalFile);
        }

        /** @var array{uploaded: int, file?: string, description?: string} $file */
        return $file;
    }

}
