<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

/**
 * Value object representing a parsed shortcode match.
 */
final class ShortcodeMatch
{
    public const TYPE_DOWNLOAD = 'download';
    public const TYPE_CATEGORY = 'category';
    public const TYPE_SEARCH = 'search';

    public function __construct(
        public readonly string $type,
        public readonly string $fullMatch,
        public readonly int $offset,
        public readonly ?int $id = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $layoutAlias = null,
        public readonly ?int $limit = null,
    ) {
    }
}
