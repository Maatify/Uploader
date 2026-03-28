<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-03-21
 * Time: 12:59 PM
 */

namespace Maatify\Uploader;

abstract class DownloadStreamFile
{
    protected int|string $file_path;
    protected string $file_saved_name = 'doc';
    
    public function DownloadFile(): void
    {
        if (!file_exists((string)$this->file_path)) {
            header("Location: https://" . $_SERVER['HTTP_HOST'] . "/404.php");
        }else{
            $file_type = strtolower(pathinfo((string)$this->file_path, PATHINFO_EXTENSION));
            header('Content-Disposition: attachment;filename="' . $this->file_saved_name . '-' . time() . '.' . $file_type . '"');
            $file = fopen((string)$this->file_path, "rb");
            if ($file) {
                while (! feof($file)) {
                    print(fread($file, 1024 * 8));
                    flush();
                    if (connection_status() != 0) {
                        fclose($file);
                        die();
                    }
                }
                fclose($file);
            }
        }
    }
}