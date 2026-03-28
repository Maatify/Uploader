<?php

namespace Tests;

/**
 * Created by Maatify.dev
 * User: Maatify.dev
 * Date: 2024-10-27
 * Time: 9:20 AM
 * https://www.Maatify.dev
 */

use Maatify\Uploader\Images\UploadImage;
use PHPUnit\Framework\TestCase;

define('PHPUNIT_TEST', true);

class UploadImageTest extends TestCase
{
    protected UploadImage $uploadImage;
    protected string $uploadDir;

    protected function setUp(): void
    {
        // Use a writable temp directory for CI safety
        $this->uploadDir = sys_get_temp_dir() . '/uploader_tests';

        $this->uploadImage = new UploadImage();
        $this->uploadImage
            ->setUploadFolder($this->uploadDir)
            ->setUploadForId(1)
            ->setMaxSize(2); // 2 MB size limit
    }

    protected function tearDown(): void
    {
        $file = $this->uploadImage->getFileTarget();

        if ($file && file_exists($file)) {
            unlink($file);
        }
    }

    public function testSuccessfulImageUpload()
    {
        // Simulate a valid PNG file upload
        $_FILES['file'] = [
            'name' => 'test.png',
            'type' => 'image/png',
            'tmp_name' => __DIR__ . '/test_files/small_image.png',
            'error' => UPLOAD_ERR_OK,
            'size' => 500 * 1024, // 500 KB
        ];

        $result = $this->uploadImage->Upload();

        $this->assertArrayHasKey('uploaded', $result);
        $this->assertEquals(1, $result['uploaded']);
        $this->assertArrayHasKey('file', $result);
        $this->assertFileExists($this->uploadImage->getFileTarget());
    }

    public function testUnsupportedFileType()
    {
        // Simulate an unsupported file type (e.g., text file)
        $_FILES['file'] = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => __DIR__ . '/test_files/test.txt',
            'error' => UPLOAD_ERR_OK,
            'size' => 100 * 1024, // 100 KB
        ];

        $result = $this->uploadImage->Upload();

        $this->assertArrayHasKey('uploaded', $result);
        $this->assertEquals(0, $result['uploaded']);
        $this->assertEquals('Unsupported file type.', $result['description']);
    }

    public function testFileTooLarge()
    {
        // Simulate a file larger than the maximum allowed size
        $_FILES['file'] = [
            'name' => 'large_image.png',
            'type' => 'image/png',
            'tmp_name' => __DIR__ . '/test_files/large_image.png',
            'error' => UPLOAD_ERR_OK,
            'size' => 5 * 1024 * 1024, // 5 MB
        ];

        $result = $this->uploadImage->Upload();

        $this->assertArrayHasKey('uploaded', $result);
        $this->assertEquals(0, $result['uploaded']);
        $this->assertStringContainsString('Your file is too large', $result['description']);
    }

    public function testMissingFile()
    {
        // Simulate a missing file upload (no file in $_FILES)
        $_FILES['file'] = [];

        $result = $this->uploadImage->Upload();

        $this->assertArrayHasKey('uploaded', $result);
        $this->assertEquals(0, $result['uploaded']);
        $this->assertEquals('Missing file post.', $result['description']);
    }
}
