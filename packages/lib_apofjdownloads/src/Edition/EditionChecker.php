<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Edition;

/**
 * Determines whether this installation is the Pro or Lite (free) edition.
 *
 * Pro status is identified by the presence of a valid `.edition` file
 * containing a SHA-256 token that matches the expected build salt.
 * Fail-closed: if the file is missing or invalid, the edition is Lite.
 */
final class EditionChecker implements EditionCheckerInterface
{
    private string $editionFilePath;
    private string $buildSalt;

    public function __construct(string $editionFilePath, string $buildSalt = 'default')
    {
        $this->editionFilePath = $editionFilePath;
        $this->buildSalt = $buildSalt;
    }

    public function isPro(): bool
    {
        if (!file_exists($this->editionFilePath)) {
            return false;
        }

        $content = trim((string) file_get_contents($this->editionFilePath));

        if (strlen($content) !== 64 || !ctype_xdigit($content)) {
            return false;
        }

        return hash_equals($this->getExpectedToken(), $content);
    }

    public function isLite(): bool
    {
        return !$this->isPro();
    }

    /**
     * Generate the expected token for the current build salt.
     *
     * This is also used by the build scripts to create the `.edition` file.
     */
    public static function generateToken(string $buildSalt): string
    {
        return hash('sha256', 'apofjdl-pro-edition-' . $buildSalt);
    }

    private function getExpectedToken(): string
    {
        return self::generateToken($this->buildSalt);
    }
}
