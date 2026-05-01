<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Log;

/**
 * Contract for download log persistence.
 */
interface DownloadLogStoreInterface
{
    /**
     * Persist a download log entry.
     */
    public function log(DownloadLogEntry $entry): bool;
}
