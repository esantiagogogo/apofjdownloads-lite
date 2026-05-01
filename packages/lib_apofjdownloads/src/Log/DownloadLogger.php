<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Log;

/**
 * Records download events with GDPR-compliant IP hashing.
 */
class DownloadLogger
{
    private DownloadLogStoreInterface $store;
    private IpHasher $hasher;

    public function __construct(DownloadLogStoreInterface $store, IpHasher $hasher)
    {
        $this->store = $store;
        $this->hasher = $hasher;
    }

    /**
     * Log a download event.
     */
    public function logDownload(
        int $downloadId,
        int $fileId,
        int $userId,
        string $ipAddress,
        string $userAgent,
        string $status = DownloadLogEntry::COMPLETED,
    ): void {
        $entry = new DownloadLogEntry(
            downloadId: $downloadId,
            fileId: $fileId,
            userId: $userId,
            ipHash: $this->hasher->hash($ipAddress),
            userAgent: $userAgent,
            downloadedAt: new \DateTimeImmutable(),
            status: $status,
        );

        $this->store->log($entry);
    }
}
