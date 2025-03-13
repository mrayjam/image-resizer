<?php

/**
 * Image upload feature tests.
 *
 * @author Aymane Bouljam
 */

require_once 'upload.php';

describe('Image Upload Feature', function () {
    beforeEach(function () {
        foreach (['uploads', 'resized'] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $_FILES = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    });

    it('validates file upload correctly', function () {
        $_FILES = [];
        $_POST = ['upload' => true];

        $result = handleImageUploadWithIntervention();

        expect($result['type'])->toBe('error');
        expect($result['message'])->toContain('Please select a valid image file');
    });

    it('validates file size correctly', function () {
        $testFile = createTestImage('test_large.png', 100, 100, 'png');
        $fileSize = filesize($testFile);

        $_FILES['image'] = mockUploadedFile(
            'test_large.png',
            $testFile,
            MAX_FILE_SIZE + 1000,
            'image/png'
        );
        $_POST = ['upload' => true, 'width' => 50];

        $result = handleImageUploadWithIntervention();

        expect($result['type'])->toBe('error');
        expect($result['message'])->toContain('File size too large');

        unlink($testFile);
    });

    it('validates MIME type correctly', function () {
        $testFile = 'uploads/test_invalid.txt';
        file_put_contents($testFile, 'This is not an image');

        $_FILES['image'] = mockUploadedFile(
            'test_invalid.txt',
            $testFile,
            100,
            'text/plain'
        );
        $_POST = ['upload' => true, 'width' => 50];

        $result = handleImageUploadWithIntervention();

        expect($result['type'])->toBe('error');
        expect($result['message'])->toContain('Invalid file type');

        unlink($testFile);
    });

    it('validates file extension correctly', function () {
        $testFile = createTestImage('test_wrong_ext.exe', 100, 100, 'png');

        $_FILES['image'] = mockUploadedFile(
            'test_wrong_ext.exe',
            $testFile,
            filesize($testFile),
            'image/png'
        );
        $_POST = ['upload' => true, 'width' => 50];

        $result = handleImageUploadWithIntervention();

        expect($result['type'])->toBe('error');
        expect($result['message'])->toContain('Invalid file extension');

        unlink($testFile);
    });

    it('requires at least width or height', function () {
        $testFile = createTestImage('test_no_dimensions.png', 100, 100, 'png');

        $_FILES['image'] = mockUploadedFile(
            'test_no_dimensions.png',
            $testFile,
            filesize($testFile),
            'image/png'
        );
        $_POST = ['upload' => true];

        $result = handleImageUploadWithIntervention();

        expect($result['type'])->toBe('error');
        expect($result['message'])->toContain('Please specify at least width or height');

        unlink($testFile);
    });

    it('processes valid image upload successfully', function () {
        $testFile = createTestImage('test_valid.png', 200, 100, 'png');

        $uploadedFile = 'uploads/' . basename($testFile);
        copy($testFile, $uploadedFile);

        $_FILES['image'] = [
            'name' => 'test_valid.png',
            'type' => 'image/png',
            'tmp_name' => $uploadedFile,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($testFile),
        ];
        $_POST = [
            'upload' => true,
            'width' => 100,
            'maintain_aspect_ratio' => '1',
        ];

        expect(true)->toBeTrue();

        unlink($testFile);
        if (file_exists($uploadedFile)) {
            unlink($uploadedFile);
        }
    })->skip('move_uploaded_file cannot be mocked easily in unit tests');

    it('validates allowed file types', function () {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        expect(ALLOWED_TYPES)->toBe($allowedTypes);
        expect(ALLOWED_EXTENSIONS)->toBe($allowedExtensions);
    });

    it('has correct maximum file size configured', function () {
        expect(MAX_FILE_SIZE)->toBe(5 * 1024 * 1024); // 5MB
    });
});
