<?php

declare(strict_types=1);

namespace ImageResizer;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\UidProcessor;

/**
 * Logger utility class for the Image Resizer application.
 *
 * @author Aymane Bouljam
 */
class Logger
{
    private static ?MonologLogger $instance = null;

    /**
     * Get the logger instance (singleton pattern).
     *
     * @return MonologLogger
     * @throws \Exception
     */
    public static function getInstance(): MonologLogger
    {
        if (self::$instance === null) {
            self::$instance = self::createLogger();
        }

        return self::$instance;
    }

    /**
     * Reset the logger instance (for testing purposes).
     *
     * @return void
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Create and configure the logger.
     *
     * @return MonologLogger
     * @throws \Exception
     */
    private static function createLogger(): MonologLogger
    {
        $logger = new MonologLogger('image-resizer');

        $logger->pushProcessor(new UidProcessor());

        $logger->pushHandler(new StreamHandler(
            __DIR__ . '/../logs/app.log',
            Level::Debug
        ));

        $logger->pushHandler(new StreamHandler(
            __DIR__ . '/../logs/error.log',
            Level::Error
        ));

        return $logger;
    }

    /**
     * Log image processing start.
     *
     * @param string $filename
     * @param int $originalWidth
     * @param int $originalHeight
     * @param int|null $targetWidth
     * @param int|null $targetHeight
     * @return void
     */
    public static function logImageProcessingStart(
        string $filename,
        int $originalWidth,
        int $originalHeight,
        ?int $targetWidth,
        ?int $targetHeight
    ): void {
        self::getInstance()->info('Image processing started', [
            'filename' => $filename,
            'original_dimensions' => "{$originalWidth}x{$originalHeight}",
            'target_dimensions' => ($targetWidth ?? 'auto') . 'x' . ($targetHeight ?? 'auto'),
        ]);
    }

    /**
     * Log successful image processing.
     *
     * @param string $filename
     * @param int $newWidth
     * @param int $newHeight
     * @param string $outputPath
     * @param float $processingTime
     * @return void
     */
    public static function logImageProcessingSuccess(
        string $filename,
        int $newWidth,
        int $newHeight,
        string $outputPath,
        float $processingTime
    ): void {
        self::getInstance()->info('Image processing completed successfully', [
            'filename' => $filename,
            'new_dimensions' => "{$newWidth}x{$newHeight}",
            'output_path' => $outputPath,
            'processing_time_ms' => round($processingTime * 1000, 2),
        ]);
    }

    /**
     * Log image processing error.
     *
     * @param string $filename
     * @param string $error
     * @param array $context
     * @return void
     */
    public static function logImageProcessingError(string $filename, string $error, array $context = []): void
    {
        self::getInstance()->error('Image processing failed', array_merge([
            'filename' => $filename,
            'error' => $error,
        ], $context));
    }

    /**
     * Log file upload validation error.
     *
     * @param string $filename
     * @param string $error
     * @param array $fileInfo
     * @return void
     */
    public static function logUploadValidationError(string $filename, string $error, array $fileInfo = []): void
    {
        self::getInstance()->warning('File upload validation failed', [
            'filename' => $filename,
            'error' => $error,
            'file_info' => $fileInfo,
        ]);
    }

    /**
     * Log general application info.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function logInfo(string $message, array $context = []): void
    {
        self::getInstance()->info($message, $context);
    }

    /**
     * Log application warning.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function logWarning(string $message, array $context = []): void
    {
        self::getInstance()->warning($message, $context);
    }

    /**
     * Log application error.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function logError(string $message, array $context = []): void
    {
        self::getInstance()->error($message, $context);
    }
}
