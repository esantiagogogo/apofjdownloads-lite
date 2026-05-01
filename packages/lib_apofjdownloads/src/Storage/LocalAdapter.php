<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Storage;

/**
 * Local filesystem storage adapter.
 *
 * Stores files on the server's local filesystem relative to a configurable
 * base path. Files are organized into subdirectories by download ID.
 */
class LocalAdapter implements StorageAdapterInterface
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
    }

    public function store(string $localPath, string $remotePath): bool
    {
        $destination = $this->resolvePath($remotePath);
        $directory = dirname($destination);

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                return false;
            }
        }

        return copy($localPath, $destination);
    }

    /**
     * @return resource
     */
    public function retrieve(string $remotePath)
    {
        $path = $this->resolvePath($remotePath);

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('File not found: %s', $remotePath));
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new \RuntimeException(sprintf('Cannot open file: %s', $remotePath));
        }

        return $handle;
    }

    public function delete(string $remotePath): bool
    {
        $path = $this->resolvePath($remotePath);

        if (!file_exists($path)) {
            return true;
        }

        return unlink($path);
    }

    public function exists(string $remotePath): bool
    {
        return file_exists($this->resolvePath($remotePath));
    }

    public function getSize(string $remotePath): int
    {
        $path = $this->resolvePath($remotePath);

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('File not found: %s', $remotePath));
        }

        $size = filesize($path);

        return $size !== false ? $size : 0;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    private function resolvePath(string $remotePath): string
    {
        // Prevent directory traversal
        $normalized = str_replace(['../', '..\\'], '', $remotePath);

        return $this->basePath . '/' . ltrim($normalized, '/\\');
    }
}
