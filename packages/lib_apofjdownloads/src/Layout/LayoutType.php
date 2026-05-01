<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

/**
 * Layout type constants.
 */
final class LayoutType
{
    public const CATEGORIES_LIST = 'categories_list';
    public const CATEGORY_VIEW = 'category_view';
    public const DOWNLOAD_LIST = 'download_list';
    public const DOWNLOAD_DETAIL = 'download_detail';
    public const DOWNLOAD_SUMMARY = 'download_summary';
    public const SEARCH_RESULTS = 'search_results';

    public const ALL = [
        self::CATEGORIES_LIST,
        self::CATEGORY_VIEW,
        self::DOWNLOAD_LIST,
        self::DOWNLOAD_DETAIL,
        self::DOWNLOAD_SUMMARY,
        self::SEARCH_RESULTS,
    ];

    public static function isValid(string $type): bool
    {
        return \in_array($type, self::ALL, true);
    }
}
