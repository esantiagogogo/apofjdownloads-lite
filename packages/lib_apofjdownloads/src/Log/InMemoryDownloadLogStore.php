<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Log;

/**
 * In-memory download log store for unit testing.
 */
class InMemoryDownloadLogStore implements DownloadLogStoreInterface
{
    /** @var array<DownloadLogEntry> */
    private array $entries = [];

    public function log(DownloadLogEntry $entry): bool
    {
        $this->entries[] = $entry;

        return true;
    }

    /**
     * Get all logged entries (for testing).
     *
     * @return array<DownloadLogEntry>
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * Get the last logged entry (for testing).
     */
    public function getLastEntry(): ?DownloadLogEntry
    {
        return $this->entries[array_key_last($this->entries)] ?? null;
    }
}
