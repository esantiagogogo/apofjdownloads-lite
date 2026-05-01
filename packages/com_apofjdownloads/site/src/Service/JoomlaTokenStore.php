<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Service;

use Apotentia\Library\ApofjDownloads\Token\TokenData;
use Apotentia\Library\ApofjDownloads\Token\TokenStoreInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Joomla database-backed token store.
 */
class JoomlaTokenStore implements TokenStoreInterface
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function save(TokenData $token): bool
    {
        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__apofjdl_tokens'))
            ->columns([
                $this->db->quoteName('token'),
                $this->db->quoteName('file_id'),
                $this->db->quoteName('user_id'),
                $this->db->quoteName('expires_at'),
                $this->db->quoteName('used'),
                $this->db->quoteName('created'),
            ])
            ->values(':token, :file_id, :user_id, :expires_at, :used, :created');

        $tokenStr = $token->token;
        $fileId = $token->fileId;
        $userId = $token->userId;
        $expiresAt = $token->expiresAt->format('Y-m-d H:i:s');
        $used = $token->used ? 1 : 0;
        $created = ($token->created ?? new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $query->bind(':token', $tokenStr)
            ->bind(':file_id', $fileId, ParameterType::INTEGER)
            ->bind(':user_id', $userId, ParameterType::INTEGER)
            ->bind(':expires_at', $expiresAt)
            ->bind(':used', $used, ParameterType::INTEGER)
            ->bind(':created', $created);

        $this->db->setQuery($query)->execute();

        return true;
    }

    public function findByToken(string $token): ?TokenData
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__apofjdl_tokens'))
            ->where($this->db->quoteName('token') . ' = :token')
            ->bind(':token', $token);

        $row = $this->db->setQuery($query)->loadObject();

        if ($row === null) {
            return null;
        }

        return new TokenData(
            token: $row->token,
            fileId: (int) $row->file_id,
            userId: (int) $row->user_id,
            expiresAt: new \DateTimeImmutable($row->expires_at),
            used: (bool) $row->used,
            usedAt: $row->used_at ? new \DateTimeImmutable($row->used_at) : null,
            created: new \DateTimeImmutable($row->created),
        );
    }

    public function markUsed(string $token): bool
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $used = 1;

        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__apofjdl_tokens'))
            ->set($this->db->quoteName('used') . ' = :used')
            ->set($this->db->quoteName('used_at') . ' = :used_at')
            ->where($this->db->quoteName('token') . ' = :token')
            ->bind(':used', $used, ParameterType::INTEGER)
            ->bind(':used_at', $now)
            ->bind(':token', $token);

        $this->db->setQuery($query)->execute();

        return $this->db->getAffectedRows() > 0;
    }

    public function deleteExpired(): int
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $query = $this->db->getQuery(true)
            ->delete($this->db->quoteName('#__apofjdl_tokens'))
            ->where($this->db->quoteName('expires_at') . ' < :now')
            ->bind(':now', $now);

        $this->db->setQuery($query)->execute();

        return $this->db->getAffectedRows();
    }
}
