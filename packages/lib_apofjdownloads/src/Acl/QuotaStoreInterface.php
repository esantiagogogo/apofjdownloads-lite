<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * Contract for quota tracking persistence.
 */
interface QuotaStoreInterface
{
    /**
     * Get the current usage count for a user in a given period.
     */
    public function getUsage(int $userId, string $period): int;

    /**
     * Increment the usage count for a user in a given period.
     */
    public function incrementUsage(int $userId, string $period): void;

    /**
     * Get the configured limit for a user in a given period.
     * Returns 0 for unlimited.
     */
    public function getLimit(int $userId, string $period): int;
}
