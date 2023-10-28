<?php
/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-10-27
 * Time: 8:56 AM
 * https://www.Maatify.dev
 */

namespace Maatify\Uploader;

use ErrorException;
use Maatify\Logger\Logger;
use Maatify\Uploader\Mime\MimeValidate;

abstract class UploadBase extends MimeValidate
{
    protected int|string $uploaded_for_id;
    protected string $upload_folder;
    protected string $file_target;
    protected string $file_name;
    protected string $extension;
    const MB = 1048576; // 1 MB in bytes

    abstract protected function allowedExtensions(): array;
    abstract protected function validateMime(string $mime): string;

    protected function Upload(): array
    {
        if (empty($_FILES["file"]) || !is_array($_FILES["file"]) || empty($_FILES["file"]["tmp_name"])) {
            return $this->ReturnError('Missing file post.');
        }

        // Check for any upload errors
        if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
            return $this->ReturnError('File upload error: ' . $_FILES["file"]["error"]);
        }

        // Get the MIME type of the uploaded file
        $this->extension = mime_content_type($_FILES["file"]["tmp_name"]);
        $this->extension = $this->validateMime($this->extension);

        if (empty($this->extension) || !in_array($this->extension, $this->allowedExtensions())) {
            return $this->ReturnError('Unsupported file type.');
        }

        // Generate a unique filename if none is provided
        if (empty($this->file_name)) {
            $fileName = round(microtime(true) * 1000) . uniqid();
            $file = $this->uploaded_for_id . '_' . time() . "_" . $fileName . uniqid() . '.' . $this->extension;
        } else {
            $file = $this->file_name . '.' . $this->extension;
        }

        // Sanitize the filename using basename and preg_replace for security
        $file = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($file));

        // Set the target path for the file upload
        $target_path = realpath($this->upload_folder) . '/' . $file;
        if (strpos($target_path, realpath($this->upload_folder)) !== 0) {
            return $this->ReturnError('Invalid file path.');
        }

        $this->file_target = $target_path;

        // Check the file size against the maximum allowed size (if defined)
        if (!empty($this->max_size) && $_FILES["file"]["size"] > $this->max_size) {
            return $this->ReturnError("Your file is too large, cannot be more than " . ($this->max_size / self::MB) . " MB.");
        }

        // Create the upload folder if it doesn't exist
        if (!$this->createUploadFolder()) {
            return $this->ReturnError('Failed to create upload folder.');
        }

        // Move the uploaded file to the target directory and verify success
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $this->file_target)) {
            return $this->ReturnSuccess($file);
        }

        return $this->ReturnError('Failed to move uploaded file.');
    }

    protected function createUploadFolder(): bool
    {
        if (!file_exists($this->upload_folder)) {
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
                mkdir($this->upload_folder, 0777, true);
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
}
