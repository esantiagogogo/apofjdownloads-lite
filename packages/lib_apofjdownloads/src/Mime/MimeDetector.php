<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Mime;

/**
 * Server-side MIME type detection using finfo/libmagic.
 *
 * Never trusts client-provided Content-Type headers.
 */
class MimeDetector
{
    /**
     * Detect the MIME type of a file using finfo.
     */
    public function detect(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf('File not found: %s', $filePath));
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($filePath);

        if ($mimeType === false) {
            return 'application/octet-stream';
        }

        return $mimeType;
    }
}
