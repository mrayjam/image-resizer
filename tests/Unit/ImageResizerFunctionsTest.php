<?php

/**
 * Image resizer functions unit tests.
 *
 * @author Aymane Bouljam
 */

require_once __DIR__ . '/../../src/upload.php';

describe('resizeImageWithIntervention function', function () {
    beforeEach(function () {
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        if (!is_dir('resized')) {
            mkdir('resized', 0755, true);
        }
    });

    it('can resize PNG image maintaining aspect ratio', function () {
        $sourceFile = createTestImage('test_source.png', 200, 100, 'png');
        $destFile = 'resized/test_dest.png';

        $result = resizeImageWithIntervention($sourceFile, $destFile, 100, null, true, 90);

        expect($result['success'])->toBeTrue();
        expect($destFile)->toBeFile();

        $imageInfo = getimagesize($destFile);
        expect($imageInfo[0])->toBe(100);
        expect($imageInfo[1])->toBe(50);

        unlink($sourceFile);
        unlink($destFile);
    });

    it('can resize JPEG image without maintaining aspect ratio', function () {
        $sourceFile = createTestImage('test_source.jpg', 200, 100, 'jpg');
        $destFile = 'resized/test_dest.jpg';

        $result = resizeImageWithIntervention($sourceFile, $destFile, 150, 200, false, 90);

        expect($result['success'])->toBeTrue();
        expect($destFile)->toBeFile();

        $imageInfo = getimagesize($destFile);
        expect($imageInfo[0])->toBe(150);
        expect($imageInfo[1])->toBe(200);

        unlink($sourceFile);
        unlink($destFile);
    });

    it('handles non-existent source file', function () {
        set_error_handler(function (int $errno, string $errstr): bool {
            return true;
        });
        $result = resizeImageWithIntervention('uploads/does_not_exist.png', 'resized/dest.png', 100, 100, true, 90);
        restore_error_handler();

        expect($result['success'])->toBeFalse();
        expect($result['error'])->toContain('Error resizing image');
    });

    it('calculates dimensions correctly when only width is provided', function () {
        $sourceFile = createTestImage('test_source.png', 400, 200, 'png');
        $destFile = 'resized/test_dest.png';

        $result = resizeImageWithIntervention($sourceFile, $destFile, 200, null, true, 90);

        expect($result['success'])->toBeTrue();

        $imageInfo = getimagesize($destFile);
        expect($imageInfo[0])->toBe(200);
        expect($imageInfo[1])->toBe(100);

        unlink($sourceFile);
        unlink($destFile);
    });

    it('calculates dimensions correctly when only height is provided', function () {
        $sourceFile = createTestImage('test_source.png', 400, 200, 'png');
        $destFile = 'resized/test_dest.png';

        $result = resizeImageWithIntervention($sourceFile, $destFile, null, 100, true, 90);

        expect($result['success'])->toBeTrue();

        $imageInfo = getimagesize($destFile);
        expect($imageInfo[0])->toBe(200);
        expect($imageInfo[1])->toBe(100);

        unlink($sourceFile);
        unlink($destFile);
    });

    it('respects quality parameter', function () {
        $sourceFile = createTestImage('test_source.jpg', 300, 150, 'jpg');
        $destFile = 'resized/test_dest.jpg';

        $result = resizeImageWithIntervention($sourceFile, $destFile, 150, 75, true, 50);

        expect($result['success'])->toBeTrue();
        expect($destFile)->toBeFile();

        unlink($sourceFile);
        unlink($destFile);
    });
});
