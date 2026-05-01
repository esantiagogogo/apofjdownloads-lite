<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Token;

/**
 * Contract for token persistence.
 *
 * Implementations provide the storage backend (database, cache, in-memory).
 */
interface TokenStoreInterface
{
    /**
     * Save a token to the store.
     */
    public function save(TokenData $token): bool;

    /**
     * Find a token by its string value.
     */
    public function findByToken(string $token): ?TokenData;

    /**
     * Mark a token as used.
     */
    public function markUsed(string $token): bool;

    /**
     * Delete all expired tokens. Returns the number deleted.
     */
    public function deleteExpired(): int;
}
