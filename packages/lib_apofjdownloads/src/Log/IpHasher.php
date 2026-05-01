<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Log;

/**
 * GDPR-compliant IP address hasher.
 *
 * Hashes IP addresses with a salt so the original IP is never stored.
 * The same IP + salt always produces the same hash for correlation,
 * but the original IP cannot be recovered.
 */
class IpHasher
{
    private string $salt;

    public function __construct(string $salt)
    {
        $this->salt = $salt;
    }

    /**
     * Hash an IP address.
     *
     * @return string 64-character hex SHA-256 hash.
     */
    public function hash(string $ipAddress): string
    {
        return hash('sha256', $this->salt . $ipAddress);
    }
}
