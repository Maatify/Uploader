<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2023-09-25
 * Time: 12:01 PM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader;

abstract class UploadFolderCreate
{
    protected string $upload_folder;

    protected function CreatUploadFolder(): void
    {
        if (! file_exists($this->upload_folder)) {
            mkdir($this->upload_folder);
            $f = @fopen($this->upload_folder . '/index.php', 'a+');
            if ($f) {
                @fputs(
                    $f,
                    '<?php' . PHP_EOL
                    . 'header("Location: https://" . $_SERVER[\'HTTP_HOST\'] . "/404.php");'
                    . PHP_EOL
                );
                @fclose($f);
            }
        }
    }
}