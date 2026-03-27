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
use Maatify\Uploader\Storage\StorageAdapterInterface;

abstract class UploadBase extends MimeValidate
{
    protected int|string $uploaded_for_id;
    protected string $upload_folder;
    protected string $file_target = '';
    protected string $file_name;
    protected string $extension;

    protected ?StorageAdapterInterface $storageAdapter = null;
    protected bool $skipStoragePush = false;

    public function setStorageAdapter(StorageAdapterInterface $adapter): static
    {
        $this->storageAdapter = $adapter;
        return $this;
    }

    protected function pushToStorage(string $localPath, string $relativePath): void
    {
        if ($this->storageAdapter === null || $this->skipStoragePush) {
            return;
        }
        $this->storageAdapter->upload($localPath, $relativePath);
        @unlink($localPath);
    }
    /**
     * Set the upload folder.
     *
     * @param string $folder
     * @return self
     */
    public function setUploadFolder(string $folder): self
    {
        $this->upload_folder = rtrim($folder, '/'); // Ensure no trailing slash.
        return $this;
    }


    /**
     * Set the upload For ID.
     *
     * @param int|string $uploaded_for_id
     * @return self
     */
    public function setUploadForId(int|string $uploaded_for_id): self
    {
        $this->uploaded_for_id = $uploaded_for_id;
        return $this;
    }


    /**
     * Set the File Target.
     *
     * @param string $file_target
     * @return self
     */
    public function setFileTarget(string $file_target): self
    {
        $this->file_target = $file_target;
        return $this;
    }


    /**
     * Get the File Target.
     *
     * @return string
     */
    public function getFileTarget(): string
    {
        return $this->file_target;
    }


    /**
     * Set the File Target.
     *
     * @param string $file_name
     * @return self
     */
    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;
        return $this;
    }


    /**
     * Set the Extension.
     *
     * @param string $extension
     * @return self
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }


    /**
     * @return array<int, string>
     */
    abstract protected function allowedExtensions(): array;

    /**
     *  @return string
     */
    abstract protected function validateMime(string $mime): string;

    /**
     * @return array{uploaded: int, file?: string, description?: string}
     */
    public function upload(): array
    {
        if (empty($_FILES["file"]) || !is_array($_FILES["file"]) || empty($_FILES["file"]["tmp_name"])) {
            return $this->returnError('Missing file post.');
        }

        // Check for any upload errors
        if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
            return $this->returnError('File upload error: ' . $_FILES["file"]["error"]);
        }

        // Get the MIME type of the uploaded file
        $mime = mime_content_type((string)$_FILES["file"]["tmp_name"]);
        if ($mime === false) {
            return $this->returnError('Could not determine mime type.');
        }
        $this->extension = $this->validateMime($mime);

        if (empty($this->extension) || !in_array($this->extension, $this->allowedExtensions())) {
            return $this->returnError('Unsupported file type.');
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
        // Set the target path for the file upload
        $basePath = (string)realpath($this->upload_folder);
        $target_path = $basePath . '/' . $file;

        if (! str_starts_with($target_path, $basePath)) {
            return $this->returnError('Invalid file path.');
        }

        $this->file_target = $target_path;

        // Check the file size against the maximum allowed size (if defined)
        if (!empty($this->max_size) && $_FILES["file"]["size"] > $this->max_size) {
            return $this->returnError("Your file is too large, cannot be more than " . ($this->max_size / self::MB) . " MB.");
        }

        // Create the upload folder if it doesn't exist
        if (!$this->createUploadFolder()) {
            return $this->returnError('Failed to create upload folder.');
        }

        // Move the uploaded file to the target directory and verify success
        if (defined('PHPUNIT_TEST') || getenv('PHPUNIT_TEST') === '1') {
            if (copy($_FILES["file"]["tmp_name"], $this->file_target)) {
                $this->pushToStorage($this->file_target, (string)$file);
                return $this->returnSuccess((string)$file);
            }
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $this->file_target)) {
                $this->pushToStorage($this->file_target, (string)$file);
                return $this->returnSuccess((string)$file);
            }
        }

        return $this->returnError('Failed to move uploaded file.');
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
