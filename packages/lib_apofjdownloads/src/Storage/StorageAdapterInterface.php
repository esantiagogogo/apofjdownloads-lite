<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Storage;

/**
 * Contract for file storage adapters.
 */
interface StorageAdapterInterface
{
    /**
     * Store a file from a local path to the adapter's storage.
     */
    public function store(string $localPath, string $remotePath): bool;

    /**
     * Retrieve a file as a readable stream.
     *
     * @return resource
     */
    public function retrieve(string $remotePath);

    /**
     * Delete a file from storage.
     */
    public function delete(string $remotePath): bool;

    /**
     * Check whether a file exists in storage.
     */
    public function exists(string $remotePath): bool;

    /**
     * Get the size of a file in bytes.
     */
    public function getSize(string $remotePath): int;
}
