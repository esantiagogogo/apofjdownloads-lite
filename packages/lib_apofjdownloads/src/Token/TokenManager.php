<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Token;

/**
 * Manages one-time download tokens for secure file serving.
 *
 * Tokens are 64-character hex strings generated from random_bytes(32).
 * Each token is single-use and has a configurable TTL.
 */
class TokenManager
{
    private TokenStoreInterface $store;
    private int $defaultTtl;

    /**
     * @param TokenStoreInterface $store      Token persistence backend.
     * @param int                 $defaultTtl Default time-to-live in seconds.
     */
    public function __construct(TokenStoreInterface $store, int $defaultTtl = 3600)
    {
        $this->store = $store;
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * Generate a new one-time download token.
     *
     * @return string The 64-character hex token string.
     */
    public function generateToken(int $fileId, int $userId, ?int $ttlSeconds = null): string
    {
        $ttl = $ttlSeconds ?? $this->defaultTtl;
        $tokenString = bin2hex(random_bytes(32));
        $now = new \DateTimeImmutable();

        $tokenData = new TokenData(
            token: $tokenString,
            fileId: $fileId,
            userId: $userId,
            expiresAt: $now->modify(sprintf('+%d seconds', $ttl)),
            used: false,
            usedAt: null,
            created: $now,
        );

        $this->store->save($tokenData);

        return $tokenString;
    }

    /**
     * Validate a token without consuming it.
     *
     * Returns TokenData if valid, null if invalid/expired/used.
     */
    public function validateToken(string $token): ?TokenData
    {
        $tokenData = $this->store->findByToken($token);

        if ($tokenData === null) {
            return null;
        }

        if ($tokenData->isExpired()) {
            return null;
        }

        if ($tokenData->used) {
            return null;
        }

        return $tokenData;
    }

    /**
     * Consume a token (validate and mark as used).
     *
     * @return bool True if the token was valid and consumed, false otherwise.
     */
    public function consumeToken(string $token): bool
    {
        if ($this->validateToken($token) === null) {
            return false;
        }

        return $this->store->markUsed($token);
    }

    /**
     * Purge all expired tokens from the store.
     *
     * @return int Number of tokens deleted.
     */
    public function purgeExpired(): int
    {
        return $this->store->deleteExpired();
    }
}
