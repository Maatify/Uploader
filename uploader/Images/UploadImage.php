<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-07-04
 * Time: 8:36 PM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Images;

use ErrorException;
use Maatify\Logger\Logger;
use Maatify\Uploader\Mime\Mime2extPDF;

class UploadImage extends Mime2extPDF
{
    protected int|string $uploaded_for_id;
    protected string $upload_folder;
    protected string $file_target;
    protected string $file_name;

    protected string $extension;

    protected function Upload(): array
    {
        if(! empty($_FILES["file"]) && is_array($_FILES["file"]) && !empty($_FILES["file"]["tmp_name"])) {
            $this->extension = mime_content_type($_FILES["file"]["tmp_name"]);
//            $extension = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            if (! empty($this->extension)) {
                $this->extension = $this->mime2extImage($this->extension); // extract extension from mime type
                if(empty($this->extension)){
                    $this->extension = $this->mime2extPDF($this->extension); // extract extension from mime type
                }
                if (empty($this->file_name)) {
                    $fileName = round(microtime(true) * 1000) . uniqid();
                    $file = $this->uploaded_for_id . '_' . time() . "_" . $fileName . uniqid() . '.' . $this->extension;
                } else {
                    $file = $this->file_name . '.' . $this->extension;
                }

                $this->file_target = $this->upload_folder . "/" . $file;


                if (! empty($this->max_size)) {
                    if ($_FILES["file"]["size"] > $this->max_size * 1024) {
                        return $this->ReturnError("your file is too large, cannot be more than $this->max_size MB");
                    }
                }

                $size = getimagesize($_FILES["file"]["tmp_name"]);
                if (empty($size)) {
                    return $this->ReturnError("your file is not an image");
                }

                if (! empty($this->max_width)) {
                    if ($size[0] > $this->max_width) {
                        return $this->ReturnError("your file Width cannot be more than $this->max_width");
                    }
                }

                if (! empty($this->max_height)) {
                    if ($size[1] > $this->max_height) {
                        return $this->ReturnError("your file Height cannot be more than $this->max_height");
                    }
                }



                if(!file_exists($this->upload_folder)){
                    set_error_handler(/**
                     * @throws ErrorException
                     */ function($errno, $errstr, $errfile, $errline) {
                        // error was suppressed with the @-operator
                        if (0 === error_reporting()) {
                            return false;
                        }

                        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
                    });

                    try {
                        mkdir($this->file_target);
                    } catch (ErrorException $e) {
                        Logger::RecordLog($e, 'uploader_error');
                        return $this->ReturnError('Path Not Found');
                    }
                }

                move_uploaded_file($_FILES["file"]["tmp_name"], $this->file_target);

                return $this->ReturnSuccess($file);
            } else {
                return $this->ReturnError('Missing image Post');
            }
        }else{

            return $this->ReturnError('Missing image Post');
        }
    }

    private function ReturnError($description): array
    {
        return ['uploaded'=>0, 'description'=> $description];
    }

    protected function ReturnSuccess($file): array
    {
        return ['uploaded'=>1, 'image'=>$file];
    }
}