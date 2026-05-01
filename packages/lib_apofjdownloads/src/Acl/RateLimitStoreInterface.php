<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * Contract for rate limit tracking persistence.
 */
interface RateLimitStoreInterface
{
    /**
     * Get the number of requests by an identifier within a time window.
     */
    public function getRecentRequestCount(string $identifier, int $windowSeconds): int;

    /**
     * Record a new request for the given identifier.
     */
    public function recordRequest(string $identifier): void;
}
