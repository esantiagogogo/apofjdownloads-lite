<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Mime;

/**
 * Result of a MIME type validation check.
 */
class MimeValidationResult
{
    public function __construct(
        public readonly bool $passed,
        public readonly string $detectedMime,
        public readonly string $reason = '',
    ) {
    }
}
