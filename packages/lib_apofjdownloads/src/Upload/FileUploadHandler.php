<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Upload;

use Apotentia\Library\ApofjDownloads\Edition\EditionCheckerInterface;
use Apotentia\Library\ApofjDownloads\Mime\MimeDetector;
use Apotentia\Library\ApofjDownloads\Mime\MimeValidator;
use Apotentia\Library\ApofjDownloads\Storage\StorageAdapterInterface;

/**
 * Handles file upload processing: validation, storage, hashing, and MIME detection.
 *
 * Never trusts client-provided metadata. All file properties (MIME type, size,
 * hashes) are computed server-side from the actual file contents.
 */
class FileUploadHandler
{
    private const UPLOAD_CEILING_BYTES = 5242880; // 5 * 1024 * 1024

    /** @var array<int, string> PHP upload error messages */
    private const UPLOAD_ERRORS = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds the server upload size limit.',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds the form upload size limit.',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on the server.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
    ];

    private StorageAdapterInterface $storage;
    private MimeDetector $mimeDetector;
    private ?MimeValidator $mimeValidator;
    private ?EditionCheckerInterface $editionChecker;

    public function __construct(
        StorageAdapterInterface $storage,
        MimeDetector $mimeDetector,
        ?MimeValidator $mimeValidator = null,
        ?EditionCheckerInterface $editionChecker = null,
    ) {
        $this->storage = $storage;
        $this->mimeDetector = $mimeDetector;
        $this->mimeValidator = $mimeValidator;
        $this->editionChecker = $editionChecker;
    }

    /**
     * Process a single file upload from $_FILES data.
     *
     * @param  array  $fileData    Single entry from $_FILES (name, tmp_name, error, size, type).
     * @param  int    $downloadId  Parent download ID for path organization.
     *
     * @return FileUploadResult  Populated result with server-verified metadata.
     *
     * @throws \RuntimeException  If the upload fails validation or storage.
     */
    public function processUpload(array $fileData, int $downloadId): FileUploadResult
    {
        $this->validateUpload($fileData);
        $this->enforceUploadCeiling($fileData);

        $tmpPath = $fileData['tmp_name'];
        $originalName = $this->sanitizeFilename($fileData['name']);

        // Compute hashes from the temp file before moving
        $hashSha256 = hash_file('sha256', $tmpPath);
        $hashMd5 = hash_file('md5', $tmpPath);

        if ($hashSha256 === false || $hashMd5 === false) {
            throw new \RuntimeException('Failed to compute file hashes.');
        }

        // Detect MIME type server-side (never trust the client)
        $mimeType = $this->mimeDetector->detect($tmpPath);
        $mimeVerified = true;

        // Validate against allowlist if a validator is configured
        if ($this->mimeValidator !== null) {
            $validation = $this->mimeValidator->validate($tmpPath);

            if (!$validation->passed) {
                throw new \RuntimeException(sprintf(
                    'Upload rejected: %s',
                    $validation->reason,
                ));
            }
        }

        // Build storage path: {download_id}/{hash_prefix}_{filename}
        $hashPrefix = substr($hashSha256, 0, 8);
        $storagePath = sprintf('%d/%s_%s', $downloadId, $hashPrefix, $originalName);

        // Store via adapter
        if (!$this->storage->store($tmpPath, $storagePath)) {
            throw new \RuntimeException(sprintf('Failed to store file: %s', $originalName));
        }

        // Get authoritative file size from storage
        $size = $this->storage->getSize($storagePath);

        return new FileUploadResult(
            filename: $originalName,
            filepath: $storagePath,
            size: $size,
            mimeType: $mimeType,
            mimeVerified: $mimeVerified,
            hashSha256: $hashSha256,
            hashMd5: $hashMd5,
        );
    }

    private function enforceUploadCeiling(array $fileData): void
    {
        if ($this->editionChecker === null || $this->editionChecker->isPro()) {
            return;
        }

        $actualSize = filesize($fileData['tmp_name']);

        if ($actualSize !== false && $actualSize > self::UPLOAD_CEILING_BYTES) {
            throw new \RuntimeException(sprintf(
                'File size (%s) exceeds the 5 MB limit in APO FJ Downloads Lite. '
                . 'Upgrade to Pro for unlimited file uploads.',
                $this->formatBytes($actualSize),
            ));
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        return round($bytes / 1024, 1) . ' KB';
    }

    /**
     * Validate the raw upload data from $_FILES.
     *
     * @throws \RuntimeException If the upload is invalid.
     */
    private function validateUpload(array $fileData): void
    {
        if (!isset($fileData['error'], $fileData['tmp_name'], $fileData['name'])) {
            throw new \RuntimeException('Invalid upload data.');
        }

        $error = (int) $fileData['error'];

        if ($error !== UPLOAD_ERR_OK) {
            $message = self::UPLOAD_ERRORS[$error] ?? 'Unknown upload error.';
            throw new \RuntimeException($message);
        }

        if (empty($fileData['tmp_name']) || !$this->isUploadedFile($fileData['tmp_name'])) {
            throw new \RuntimeException('Invalid upload: file did not come from a form submission.');
        }

        if (filesize($fileData['tmp_name']) === 0) {
            throw new \RuntimeException('Uploaded file is empty.');
        }
    }

    /**
     * Check if a file was uploaded via HTTP POST.
     *
     * Extracted into a method so unit tests can override without requiring
     * real HTTP uploads.
     */
    protected function isUploadedFile(string $path): bool
    {
        return is_uploaded_file($path);
    }

    /**
     * Sanitize a filename for safe storage.
     *
     * Strips path components, replaces unsafe characters, and blocks
     * double extensions that could disguise executable files.
     */
    private function sanitizeFilename(string $filename): string
    {
        // Strip any path components (directory traversal)
        $filename = basename($filename);

        // Replace spaces and unsafe characters
        $filename = preg_replace('/[^\w\-.]/', '_', $filename);

        // Collapse multiple dots (block double extensions like file.php.jpg)
        $filename = preg_replace('/\.{2,}/', '.', $filename);

        // Block dangerous extensions anywhere in the name
        $dangerous = ['php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'phps', 'cgi', 'pl', 'py', 'sh', 'bat', 'cmd', 'exe', 'com', 'htaccess', 'htpasswd'];
        $parts = explode('.', $filename);

        if (count($parts) > 1) {
            // Check all extensions except the last one for dangerous types
            for ($i = 0, $count = count($parts) - 1; $i < $count; $i++) {
                if (in_array(strtolower($parts[$i]), $dangerous, true)) {
                    $parts[$i] = $parts[$i] . '_blocked';
                }
            }

            $filename = implode('.', $parts);
        }

        // Ensure not empty after sanitization
        if (trim($filename, '_.') === '') {
            $filename = 'unnamed_file';
        }

        return $filename;
    }
}
