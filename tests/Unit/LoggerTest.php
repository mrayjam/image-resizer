<?php

/**
 * Logger class unit tests.
 *
 * @author Aymane Bouljam
 */

use ImageResizer\Logger;

describe('Logger', function () {
    beforeEach(function () {
        Logger::resetInstance();

        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
        $logFiles = ['logs/app.log', 'logs/error.log'];
        foreach ($logFiles as $logFile) {
            if (file_exists($logFile)) {
                unlink($logFile);
            }
        }
    });

    it('can get logger instance', function () {
        $logger = Logger::getInstance();
        expect($logger)->toBeInstanceOf(\Monolog\Logger::class);
    });

    it('returns same instance on multiple calls (singleton)', function () {
        $logger1 = Logger::getInstance();
        $logger2 = Logger::getInstance();
        expect($logger1)->toBe($logger2);
    });

    it('can log info messages', function () {
        Logger::logInfo('Test info message', ['test' => true]);

        expect('logs/app.log')->toBeFile();

        $logContent = file_get_contents('logs/app.log');
        expect($logContent)->toContain('Test info message');
        expect($logContent)->toContain('INFO');
    });

    it('can log warning messages', function () {
        Logger::logWarning('Test warning message', ['warning' => true]);

        expect('logs/app.log')->toBeFile();

        $logContent = file_get_contents('logs/app.log');
        expect($logContent)->toContain('Test warning message');
        expect($logContent)->toContain('WARNING');
    });

    it('can log error messages', function () {
        Logger::logError('Test error message', ['error' => true]);

        expect('logs/app.log')->toBeFile();
        expect('logs/error.log')->toBeFile();

        $errorLogContent = file_get_contents('logs/error.log');
        expect($errorLogContent)->toContain('Test error message');
        expect($errorLogContent)->toContain('ERROR');
    });

    it('can log image processing start', function () {
        Logger::logImageProcessingStart('test.jpg', 800, 600, 400, 300);

        expect('logs/app.log')->toBeFile();

        $logContent = file_get_contents('logs/app.log');
        expect($logContent)->toContain('Image processing started');
        expect($logContent)->toContain('test.jpg');
        expect($logContent)->toContain('800x600');
        expect($logContent)->toContain('400x300');
    });

    it('can log image processing success', function () {
        Logger::logImageProcessingSuccess('test.jpg', 400, 300, 'resized/test.jpg', 0.5);

        expect('logs/app.log')->toBeFile();

        $logContent = file_get_contents('logs/app.log');
        expect($logContent)->toContain('Image processing completed successfully');
        expect($logContent)->toContain('test.jpg');
        expect($logContent)->toContain('400x300');
        expect($logContent)->toContain('500'); // processing time in ms
    });

    it('can log image processing error', function () {
        Logger::logImageProcessingError('test.jpg', 'Invalid image format', ['format' => 'invalid']);

        expect('logs/error.log')->toBeFile();

        $logContent = file_get_contents('logs/error.log');
        expect($logContent)->toContain('Image processing failed');
        expect($logContent)->toContain('test.jpg');
        expect($logContent)->toContain('Invalid image format');
    });

    it('can log upload validation error', function () {
        Logger::logUploadValidationError('test.txt', 'Invalid file type', ['type' => 'text/plain']);

        expect('logs/app.log')->toBeFile();

        $logContent = file_get_contents('logs/app.log');
        expect($logContent)->toContain('File upload validation failed');
        expect($logContent)->toContain('test.txt');
        expect($logContent)->toContain('Invalid file type');
        expect($logContent)->toContain('text/plain');
    });
});
