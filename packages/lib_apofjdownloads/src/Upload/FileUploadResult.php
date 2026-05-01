<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Upload;

/**
 * Immutable value object representing the result of a file upload.
 */
class FileUploadResult
{
    public function __construct(
        public readonly string $filename,
        public readonly string $filepath,
        public readonly int $size,
        public readonly string $mimeType,
        public readonly bool $mimeVerified,
        public readonly string $hashSha256,
        public readonly string $hashMd5,
    ) {
    }
}
