<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * Manages per-user download quotas.
 *
 * Supports daily, weekly, monthly, and total (lifetime) periods.
 * A limit of 0 means unlimited (no quota enforced).
 */
class QuotaManager
{
    private QuotaStoreInterface $store;

    public function __construct(QuotaStoreInterface $store)
    {
        $this->store = $store;
    }

    /**
     * Check if a user is within their quota for the given period.
     */
    public function checkQuota(int $userId, string $period = 'daily'): QuotaStatus
    {
        $limit = $this->store->getLimit($userId, $period);

        // Zero limit means unlimited
        if ($limit === 0) {
            $used = $this->store->getUsage($userId, $period);

            return new QuotaStatus(
                allowed: true,
                used: $used,
                limit: 0,
                remaining: PHP_INT_MAX,
                period: $period,
            );
        }

        $used = $this->store->getUsage($userId, $period);
        $remaining = max(0, $limit - $used);

        return new QuotaStatus(
            allowed: $used < $limit,
            used: $used,
            limit: $limit,
            remaining: $remaining,
            period: $period,
        );
    }

    /**
     * Increment the usage count for a user after a successful download.
     */
    public function incrementUsage(int $userId, string $period = 'daily'): void
    {
        $this->store->incrementUsage($userId, $period);
    }
}
