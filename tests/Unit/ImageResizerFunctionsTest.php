<?php

/**
 * Image resizer functions unit tests.
 *
 * @author Aymane Bouljam
 */

require_once 'upload.php';

describe('formatFileSize function', function () {
    it('formats bytes correctly', function () {
        expect(formatFileSize(0))->toBe('0 B');
        expect(formatFileSize(500))->toBe('500 B');
        expect(formatFileSize(1023))->toBe('1023 B');
    });

    it('formats kilobytes correctly', function () {
        expect(formatFileSize(1024))->toBe('1 KB');
        expect(formatFileSize(2048))->toBe('2 KB');
        expect(formatFileSize(1536))->toBe('1.5 KB');
    });

    it('formats megabytes correctly', function () {
        expect(formatFileSize(1048576))->toBe('1 MB');
        expect(formatFileSize(2097152))->toBe('2 MB');
        expect(formatFileSize(5242880))->toBe('5 MB');
    });

    it('formats gigabytes correctly', function () {
        expect(formatFileSize(1073741824))->toBe('1 GB');
        expect(formatFileSize(2147483648))->toBe('2 GB');
    });

    it('handles negative values', function () {
        expect(formatFileSize(-100))->toBe('0 B');
    });
});

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

    it('handles invalid source file', function () {
        $result = resizeImageWithIntervention('non_existent.png', 'resized/dest.png', 100, 100, true, 90);

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
