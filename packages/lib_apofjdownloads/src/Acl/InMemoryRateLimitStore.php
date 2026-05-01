<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * In-memory rate limit store for unit testing.
 */
class InMemoryRateLimitStore implements RateLimitStoreInterface
{
    /** @var array<string, array<float>> Identifier => list of timestamps */
    private array $requests = [];

    public function getRecentRequestCount(string $identifier, int $windowSeconds): int
    {
        if (!isset($this->requests[$identifier])) {
            return 0;
        }

        $cutoff = microtime(true) - $windowSeconds;
        $count = 0;

        foreach ($this->requests[$identifier] as $timestamp) {
            if ($timestamp >= $cutoff) {
                $count++;
            }
        }

        return $count;
    }

    public function recordRequest(string $identifier): void
    {
        $this->requests[$identifier][] = microtime(true);
    }
}
