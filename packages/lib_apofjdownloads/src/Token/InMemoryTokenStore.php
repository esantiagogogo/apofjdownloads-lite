<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Token;

/**
 * In-memory token store for unit testing.
 */
class InMemoryTokenStore implements TokenStoreInterface
{
    /** @var array<string, TokenData> */
    private array $tokens = [];

    public function save(TokenData $token): bool
    {
        $this->tokens[$token->token] = $token;

        return true;
    }

    public function findByToken(string $token): ?TokenData
    {
        return $this->tokens[$token] ?? null;
    }

    public function markUsed(string $token): bool
    {
        if (!isset($this->tokens[$token])) {
            return false;
        }

        $this->tokens[$token] = $this->tokens[$token]->markUsed();

        return true;
    }

    public function deleteExpired(): int
    {
        $now = new \DateTimeImmutable();
        $count = 0;

        foreach ($this->tokens as $key => $tokenData) {
            if ($tokenData->expiresAt < $now) {
                unset($this->tokens[$key]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get the number of stored tokens (for testing).
     */
    public function count(): int
    {
        return count($this->tokens);
    }
}
