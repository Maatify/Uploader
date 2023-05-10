<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-07-04
 * Time: 8:36 PM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader\Images;

use Maatify\Uploader\Mime\Mime2extPDF;

class UploadImage extends Mime2extPDF
{
    protected int $uploaded_for_id;
    protected string $upload_folder;
    protected string $file_target;
    protected string $file_name;

    protected function Upload(): array
    {
        if(! empty($_FILES['files'])) {
            //            $extension = strtolower(pathinfo($_FILES["inputFile"]["name"], PATHINFO_EXTENSION));
            $extension = mime_content_type($_FILES["files"]["name"]);
            if (! empty($extension)) {
                $extension = $this->mime2extImage($extension); // extract extension from mime type
                if(empty($extension)){
                    $extension = $this->mime2extPDF($extension); // extract extension from mime type
                }
                if (empty($this->file_name)) {
                    $fileName = round(microtime(true) * 1000) . uniqid();
                    $file = $this->uploaded_for_id . '_' . time() . "_" . $fileName . uniqid() . '.' . $extension;
                } else {
                    $file = $this->file_name . '.' . $extension;
                }

                $this->file_target = $this->upload_folder . "/" . $file;


                if (! empty($this->max_size)) {
                    if ($_FILES["files"]["size"] > $this->max_size * 1024) {
                        return $this->ReturnError("your file is too large, cannot be more than $this->max_size MB");
                    }
                }

                $size = getimagesize($_FILES["files"]["tmp_name"]);
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

                move_uploaded_file($_FILES["files"]["tmp_name"], $this->file_target);

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