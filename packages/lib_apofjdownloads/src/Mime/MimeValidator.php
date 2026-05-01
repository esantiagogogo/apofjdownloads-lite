<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Mime;

/**
 * Validates uploaded files against the MIME type allowlist.
 *
 * Combines server-side MIME detection (via MimeDetector) with allowlist
 * checking (via MimeAllowlist) to determine if a file should be accepted.
 */
class MimeValidator
{
    private MimeDetector $detector;
    private MimeAllowlist $allowlist;

    public function __construct(MimeDetector $detector, MimeAllowlist $allowlist)
    {
        $this->detector = $detector;
        $this->allowlist = $allowlist;
    }

    /**
     * Validate a file's MIME type against the allowlist.
     */
    public function validate(string $filePath, ?int $categoryId = null): MimeValidationResult
    {
        $detectedMime = $this->detector->detect($filePath);

        if (!$this->allowlist->isAllowed($detectedMime, $categoryId)) {
            return new MimeValidationResult(
                passed: false,
                detectedMime: $detectedMime,
                reason: sprintf(
                    'MIME type "%s" is not in the allowed list.',
                    $detectedMime,
                ),
            );
        }

        return new MimeValidationResult(
            passed: true,
            detectedMime: $detectedMime,
        );
    }
}
