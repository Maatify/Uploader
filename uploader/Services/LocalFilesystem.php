<?php

namespace Maatify\Uploader\Services;

use ErrorException;
use Maatify\Logger\Logger;

class LocalFilesystem
{
    public function createUploadFolder(string $upload_folder): bool
    {
        if (!file_exists($upload_folder)) {
            set_error_handler(
            /**
             * @throws ErrorException
             */
                function ($errno, $errstr, $errfile, $errline) {
                if (0 === error_reporting()) {
                    return false;
                }
                throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            });

            try {
                mkdir($upload_folder, 0777, true);
                return true;
            } catch (ErrorException $e) {
                Logger::RecordLog($e, 'uploader_error');
                return false;
            } finally {
                restore_error_handler();
            }
        }
        return true;
    }

    public function moveUploadedFile(string $tmp_name, string $file_target): bool
    {
        if (defined('PHPUNIT_TEST') || getenv('PHPUNIT_TEST') === '1') {
            return copy($tmp_name, $file_target);
        } else {
            return move_uploaded_file($tmp_name, $file_target);
        }
    }
}
