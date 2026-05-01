<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * In-memory quota store for unit testing.
 */
class InMemoryQuotaStore implements QuotaStoreInterface
{
    /** @var array<string, int> "userId:period" => usage count */
    private array $usage = [];

    /** @var array<string, int> "userId:period" => limit */
    private array $limits = [];

    /**
     * Pre-configure a limit for testing.
     */
    public function setLimit(int $userId, string $period, int $limit): void
    {
        $this->limits[$userId . ':' . $period] = $limit;
    }

    public function getUsage(int $userId, string $period): int
    {
        return $this->usage[$userId . ':' . $period] ?? 0;
    }

    public function incrementUsage(int $userId, string $period): void
    {
        $key = $userId . ':' . $period;
        $this->usage[$key] = ($this->usage[$key] ?? 0) + 1;
    }

    public function getLimit(int $userId, string $period): int
    {
        return $this->limits[$userId . ':' . $period] ?? 0;
    }
}
