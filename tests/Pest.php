<?php

/**
 * Pest configuration file.
 *
 * @author Aymane Bouljam
 */

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses()
    ->beforeEach(function () {
        cleanupTestFiles();
    })
    ->afterEach(function () {
        cleanupTestFiles();
    })
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeFile', function () {
    return $this->toBeTrue(is_file($this->value));
});

expect()->extend('toBeWritable', function () {
    return $this->toBeTrue(is_writable($this->value));
});

expect()->extend('toBeValidImageDimensions', function ($width, $height) {
    $imageInfo = getimagesize($this->value);
    expect($imageInfo[0])->toBe($width);
    expect($imageInfo[1])->toBe($height);

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Clean up test files and directories.
 *
 * @return void
 */
function cleanupTestFiles(): void
{
    $testDirs = ['uploads', 'resized'];

    foreach ($testDirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && str_contains($file, 'test_')) {
                    unlink($file);
                }
            }
        }
    }
}

/**
 * Create a test image file.
 *
 * @param string $filename
 * @param int $width
 * @param int $height
 * @param string $type
 * @return string Path to the created image
 */
function createTestImage(string $filename, int $width = 100, int $height = 100, string $type = 'png'): string
{
    $image = imagecreatetruecolor($width, $height);
    $color = imagecolorallocate($image, 255, 0, 0); // Red color
    imagefill($image, 0, 0, $color);

    $path = "uploads/{$filename}";

    match ($type) {
        'png' => imagepng($image, $path),
        'jpg', 'jpeg' => imagejpeg($image, $path),
        'gif' => imagegif($image, $path),
        'webp' => imagewebp($image, $path),
        default => imagepng($image, $path)
    };

    imagedestroy($image);

    return $path;
}

/**
 * Mock $_FILES array for file upload testing.
 *
 * @param string $filename
 * @param string $tmpName
 * @param int $size
 * @param string $type
 * @param int $error
 * @return array
 */
function mockUploadedFile(string $filename, string $tmpName, int $size, string $type, int $error = UPLOAD_ERR_OK): array
{
    return [
        'name' => $filename,
        'type' => $type,
        'tmp_name' => $tmpName,
        'error' => $error,
        'size' => $size,
    ];
}
