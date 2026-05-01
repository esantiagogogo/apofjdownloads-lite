<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * Per-user/IP rate limiter for download requests.
 *
 * Tracks requests within a 60-second sliding window and enforces
 * a configurable maximum per minute.
 */
class RateLimiter
{
    private RateLimitStoreInterface $store;
    private int $maxPerMinute;

    public function __construct(RateLimitStoreInterface $store, int $maxPerMinute = 10)
    {
        $this->store = $store;
        $this->maxPerMinute = $maxPerMinute;
    }

    /**
     * Check if a request from the given identifier is allowed.
     */
    public function isAllowed(string $identifier): bool
    {
        $count = $this->store->getRecentRequestCount($identifier, 60);

        return $count < $this->maxPerMinute;
    }

    /**
     * Record a request for the given identifier.
     */
    public function recordRequest(string $identifier): void
    {
        $this->store->recordRequest($identifier);
    }

    /**
     * Get the number of remaining allowed requests within the current window.
     */
    public function getRemainingRequests(string $identifier): int
    {
        $count = $this->store->getRecentRequestCount($identifier, 60);

        return max(0, $this->maxPerMinute - $count);
    }
}
