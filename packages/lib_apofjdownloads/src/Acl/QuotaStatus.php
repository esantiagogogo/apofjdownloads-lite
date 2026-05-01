<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * Result of a quota check.
 */
class QuotaStatus
{
    public function __construct(
        public readonly bool $allowed,
        public readonly int $used,
        public readonly int $limit,
        public readonly int $remaining,
        public readonly string $period,
    ) {
    }
}
