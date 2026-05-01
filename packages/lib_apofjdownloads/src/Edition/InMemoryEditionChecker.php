<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Edition;

/**
 * In-memory edition checker for unit testing.
 *
 * Returns a pre-configured edition status without touching the filesystem.
 */
final class InMemoryEditionChecker implements EditionCheckerInterface
{
    private bool $pro;

    public function __construct(bool $isPro = false)
    {
        $this->pro = $isPro;
    }

    public function isPro(): bool
    {
        return $this->pro;
    }

    public function isLite(): bool
    {
        return !$this->pro;
    }
}
