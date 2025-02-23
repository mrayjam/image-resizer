<?php

/**
 * Backend API handler for image upload and resizing.
 *
 * @author Aymane Bouljam
 * @version 1.0
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use ImageResizer\Logger;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

session_start();

const MAX_FILE_SIZE = 5 * 1024 * 1024;
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (php_sapi_name() !== 'cli' && !defined('PHPUNIT_RUNNING')) {
    header('Content-Type: application/json');

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }

    $result = handleImageUploadWithIntervention();
    echo json_encode($result);
}

/**
 * Handle image upload and resizing using Intervention Image.
 *
 * @return array Contains type, message, and optionally resized_path
 */
function handleImageUploadWithIntervention(): array
{
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        Logger::logUploadValidationError('unknown', 'No file uploaded or upload error');

        return ['type' => 'error', 'message' => 'Please select a valid image file.'];
    }

    $file = $_FILES['image'];

    if ($file['size'] > MAX_FILE_SIZE) {
        Logger::logUploadValidationError($file['name'], 'File size too large', ['size' => $file['size'], 'max' => MAX_FILE_SIZE]);

        return ['type' => 'error', 'message' => 'File size too large. Maximum 5MB allowed.'];
    }

    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, ALLOWED_TYPES)) {
        Logger::logUploadValidationError($file['name'], 'Invalid file type', ['type' => $fileType]);

        return ['type' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        Logger::logUploadValidationError($file['name'], 'Invalid file extension', ['extension' => $extension]);

        return ['type' => 'error', 'message' => 'Invalid file extension.'];
    }

    $width = !empty($_POST['width']) ? (int) $_POST['width'] : null;
    $height = !empty($_POST['height']) ? (int) $_POST['height'] : null;
    $maintainAspectRatio = isset($_POST['maintain_aspect_ratio']);
    $quality = !empty($_POST['quality']) ? (int) $_POST['quality'] : 90;

    if (!$width && !$height) {
        return ['type' => 'error', 'message' => 'Please specify at least width or height.'];
    }

    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = __DIR__ . '/../uploads/' . $filename;
    $resizedPath = __DIR__ . '/../resized/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        Logger::logError('Failed to move uploaded file', ['filename' => $file['name'], 'destination' => $uploadPath]);

        return ['type' => 'error', 'message' => 'Failed to upload file.'];
    }

    $startTime = microtime(true);
    $originalInfo = getimagesize($uploadPath);
    Logger::logImageProcessingStart($file['name'], $originalInfo[0], $originalInfo[1], $width, $height);

    $resizeResult = resizeImageWithIntervention($uploadPath, $resizedPath, $width, $height, $maintainAspectRatio, $quality);

    if ($resizeResult['success']) {
        $imageInfo = getimagesize($resizedPath);
        $fileSize = filesize($resizedPath);
        $processingTime = microtime(true) - $startTime;

        Logger::logImageProcessingSuccess($file['name'], $imageInfo[0], $imageInfo[1], $resizedPath, $processingTime);

        return [
            'type' => 'success',
            'message' => 'Image resized successfully!',
            'resized_path' => 'resized/' . basename($resizedPath),
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'file_size' => $fileSize,
            'file_size_formatted' => formatFileSize($fileSize),
        ];
    } else {
        unlink($uploadPath);
        Logger::logImageProcessingError($file['name'], $resizeResult['error']);

        return ['type' => 'error', 'message' => $resizeResult['error']];
    }
}

/**
 * Resize image using Intervention Image library.
 *
 * @param string $sourcePath Source image path
 * @param string $destPath Destination path for resized image
 * @param int|null $newWidth Target width
 * @param int|null $newHeight Target height
 * @param bool $maintainAspectRatio Whether to maintain aspect ratio
 * @param int $quality Image quality (1-100)
 * @return array Success status and error message
 */
function resizeImageWithIntervention(string $sourcePath, string $destPath, ?int $newWidth, ?int $newHeight, bool $maintainAspectRatio, int $quality): array
{
    try {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($sourcePath);

        if ($maintainAspectRatio) {
            if ($newWidth && $newHeight) {
                $image->scale(width: $newWidth, height: $newHeight);
            } elseif ($newWidth) {
                $image->scale(width: $newWidth);
            } elseif ($newHeight) {
                $image->scale(height: $newHeight);
            }
        } else {
            $image->resize($newWidth ?: $image->width(), $newHeight ?: $image->height());
        }

        $image->save($destPath, quality: $quality);

        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Error resizing image: ' . $e->getMessage()];
    }
}

/**
 * Format file size for display.
 *
 * @param int $bytes Size in bytes
 * @return string Formatted size string
 */
function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);

    return round($bytes, 2) . ' ' . $units[$pow];
}
