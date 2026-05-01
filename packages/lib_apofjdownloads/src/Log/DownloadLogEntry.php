<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Log;

/**
 * Immutable value object representing a single download log entry.
 */
class DownloadLogEntry
{
    public const COMPLETED         = 'completed';
    public const DENIED_ACL        = 'denied_acl';
    public const DENIED_QUOTA      = 'denied_quota';
    public const DENIED_RATE_LIMIT = 'denied_rate_limit';
    public const TOKEN_EXPIRED     = 'token_expired';
    public const TOKEN_INVALID     = 'token_invalid';

    public function __construct(
        public readonly int $downloadId,
        public readonly int $fileId,
        public readonly int $userId,
        public readonly string $ipHash,
        public readonly string $userAgent,
        public readonly \DateTimeImmutable $downloadedAt,
        public readonly string $status = self::COMPLETED,
    ) {
    }
}
