<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Mime;

/**
 * Manages MIME type allowlists for upload validation.
 *
 * Supports a global allowlist with optional per-category overrides.
 * Category overrides fully replace the global list (not additive).
 */
class MimeAllowlist
{
    /** @var array<string> */
    private array $globalAllowlist;

    /** @var array<int, array<string>> Category ID => allowed MIME types */
    private array $categoryOverrides;

    /**
     * @param array<string>              $globalAllowlist   Global list of allowed MIME types.
     * @param array<int, array<string>>  $categoryOverrides Per-category overrides (category ID => MIME list).
     */
    public function __construct(array $globalAllowlist, array $categoryOverrides = [])
    {
        $this->globalAllowlist = array_map('strtolower', $globalAllowlist);
        $this->categoryOverrides = array_map(
            fn(array $types) => array_map('strtolower', $types),
            $categoryOverrides,
        );
    }

    /**
     * Check if a MIME type is allowed.
     */
    public function isAllowed(string $mimeType, ?int $categoryId = null): bool
    {
        $allowedTypes = $this->getAllowedTypes($categoryId);

        return in_array(strtolower($mimeType), $allowedTypes, true);
    }

    /**
     * Get the list of allowed MIME types for a given context.
     *
     * @return array<string>
     */
    public function getAllowedTypes(?int $categoryId = null): array
    {
        if ($categoryId !== null && isset($this->categoryOverrides[$categoryId])) {
            return $this->categoryOverrides[$categoryId];
        }

        return $this->globalAllowlist;
    }

    /**
     * Get the default allowlist for typical website download managers.
     *
     * Covers documents, images, audio, video, archives, fonts, and common
     * data interchange formats. Excludes all executable and script types.
     *
     * @return array<string>
     */
    public static function getDefaultAllowlist(): array
    {
        return [
            // --- Documents ---
            'application/pdf',
            'text/plain',
            'text/csv',
            'text/tab-separated-values',
            'text/rtf',
            'application/rtf',
            'application/epub+zip',

            // Microsoft Office (legacy binary)
            'application/msword',
            'application/vnd.ms-excel',
            'application/vnd.ms-powerpoint',

            // Microsoft Office (OpenXML)
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.template',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',

            // OpenDocument (LibreOffice / OpenOffice)
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.graphics',
            'application/vnd.oasis.opendocument.text-template',
            'application/vnd.oasis.opendocument.spreadsheet-template',
            'application/vnd.oasis.opendocument.presentation-template',

            // --- Images ---
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
            'image/avif',
            'image/bmp',
            'image/tiff',
            'image/x-icon',
            'image/vnd.microsoft.icon',
            'image/heic',
            'image/heif',
            'image/apng',
            'image/jxl',

            // --- Audio ---
            'audio/mpeg',
            'audio/ogg',
            'audio/wav',
            'audio/x-wav',
            'audio/flac',
            'audio/x-flac',
            'audio/aac',
            'audio/mp4',
            'audio/x-m4a',
            'audio/x-aiff',
            'audio/aiff',
            'audio/opus',
            'audio/webm',
            'audio/x-ms-wma',
            'audio/midi',
            'audio/x-midi',

            // --- Video ---
            'video/mp4',
            'video/webm',
            'video/ogg',
            'video/x-msvideo',
            'video/quicktime',
            'video/x-ms-wmv',
            'video/x-flv',
            'video/x-matroska',
            'video/mpeg',
            'video/3gpp',
            'video/3gpp2',
            'video/mp2t',
            'video/x-m4v',

            // --- Archives ---
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/vnd.rar',
            'application/x-7z-compressed',
            'application/gzip',
            'application/x-gzip',
            'application/x-tar',
            'application/x-bzip2',
            'application/x-xz',
            'application/x-compress',
            'application/x-lzip',
            'application/x-lzma',
            'application/zstd',

            // --- Fonts ---
            'font/woff',
            'font/woff2',
            'font/ttf',
            'font/otf',
            'font/sfnt',
            'application/font-woff',
            'application/font-woff2',
            'application/x-font-ttf',
            'application/x-font-opentype',
            'application/vnd.ms-fontobject',

            // --- Data interchange ---
            'application/json',
            'application/xml',
            'text/xml',
            'text/html',
            'text/css',
            'text/markdown',
            'text/calendar',
            'text/vcard',
            'application/yaml',
            'application/x-yaml',

            // --- Disk images / installers (non-executable containers) ---
            'application/x-iso9660-image',
            'application/x-apple-diskimage',

            // --- Miscellaneous ---
            'application/octet-stream',
            'application/x-sqlite3',
            'application/vnd.google-earth.kml+xml',
            'application/vnd.google-earth.kmz',
            'application/gpx+xml',
        ];
    }
}
