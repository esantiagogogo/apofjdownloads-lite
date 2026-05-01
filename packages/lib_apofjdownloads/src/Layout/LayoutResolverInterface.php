<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

/**
 * Resolves layouts from a backing store (database, memory, etc.).
 */
interface LayoutResolverInterface
{
    /**
     * Resolve a layout by type and optional alias/category.
     *
     * @param  string   $type        One of LayoutType constants.
     * @param  ?string  $alias       Explicit layout alias (highest priority).
     * @param  ?int     $categoryId  Category scope for fallback.
     *
     * @return ?LayoutData  Resolved layout or null if none found.
     */
    public function resolve(string $type, ?string $alias = null, ?int $categoryId = null): ?LayoutData;

    /**
     * Resolve a layout by its primary key.
     */
    public function resolveById(int $id): ?LayoutData;

    /**
     * Resolve using the full cascade chain:
     * 1. Explicit alias
     * 2. Download-level override (from download params)
     * 3. Category-level default
     * 4. Global default
     *
     * @param  string   $type        One of LayoutType constants.
     * @param  ?string  $alias       Explicit layout alias.
     * @param  ?int     $downloadId  Download ID for download-level override.
     * @param  ?int     $categoryId  Category scope for fallback.
     *
     * @return ?LayoutData  Resolved layout or null if none found.
     */
    public function resolveWithCascade(
        string $type,
        ?string $alias = null,
        ?int $downloadId = null,
        ?int $categoryId = null,
    ): ?LayoutData;
}
