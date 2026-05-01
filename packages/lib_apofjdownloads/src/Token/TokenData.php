<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Token;

/**
 * Immutable value object representing a download token.
 */
class TokenData
{
    public function __construct(
        public readonly string $token,
        public readonly int $fileId,
        public readonly int $userId,
        public readonly \DateTimeImmutable $expiresAt,
        public readonly bool $used = false,
        public readonly ?\DateTimeImmutable $usedAt = null,
        public readonly ?\DateTimeImmutable $created = null,
    ) {
    }

    /**
     * Return a copy with used flag set.
     */
    public function markUsed(): self
    {
        return new self(
            token: $this->token,
            fileId: $this->fileId,
            userId: $this->userId,
            expiresAt: $this->expiresAt,
            used: true,
            usedAt: new \DateTimeImmutable(),
            created: $this->created,
        );
    }

    /**
     * Check if this token has expired.
     */
    public function isExpired(): bool
    {
        return $this->expiresAt < new \DateTimeImmutable();
    }
}
