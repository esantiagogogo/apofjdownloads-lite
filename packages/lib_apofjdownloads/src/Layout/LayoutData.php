<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

/**
 * Immutable value object representing a resolved layout.
 */
final class LayoutData
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $alias,
        public readonly string $type,
        public readonly string $bodyTwig,
        public readonly string $css = '',
    ) {
    }
}
