<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Service;

use Apotentia\Library\ApofjDownloads\Acl\QuotaStoreInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Joomla database-backed quota store using #__apofjdl_user_quotas.
 */
class JoomlaQuotaStore implements QuotaStoreInterface
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getUsage(int $userId, string $period): int
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('count'))
            ->from($this->db->quoteName('#__apofjdl_user_quotas'))
            ->where($this->db->quoteName('user_id') . ' = :user_id')
            ->where($this->db->quoteName('period') . ' = :period')
            ->bind(':user_id', $userId, ParameterType::INTEGER)
            ->bind(':period', $period);

        $result = $this->db->setQuery($query)->loadResult();

        return $result !== null ? (int) $result : 0;
    }

    public function incrementUsage(int $userId, string $period): void
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        // Try to update existing row
        $query = $this->db->getQuery(true)
            ->update($this->db->quoteName('#__apofjdl_user_quotas'))
            ->set($this->db->quoteName('count') . ' = ' . $this->db->quoteName('count') . ' + 1')
            ->set($this->db->quoteName('updated_at') . ' = :now')
            ->where($this->db->quoteName('user_id') . ' = :user_id')
            ->where($this->db->quoteName('period') . ' = :period')
            ->bind(':now', $now)
            ->bind(':user_id', $userId, ParameterType::INTEGER)
            ->bind(':period', $period);

        $this->db->setQuery($query)->execute();

        if ($this->db->getAffectedRows() === 0) {
            // Insert new row
            $count = 1;
            $limitValue = 0;
            $groupId = 0;

            $insert = $this->db->getQuery(true)
                ->insert($this->db->quoteName('#__apofjdl_user_quotas'))
                ->columns([
                    $this->db->quoteName('user_id'),
                    $this->db->quoteName('group_id'),
                    $this->db->quoteName('period'),
                    $this->db->quoteName('count'),
                    $this->db->quoteName('limit_value'),
                    $this->db->quoteName('period_start'),
                    $this->db->quoteName('updated_at'),
                ])
                ->values(':user_id, :group_id, :period, :count, :limit_value, :period_start, :updated_at')
                ->bind(':user_id', $userId, ParameterType::INTEGER)
                ->bind(':group_id', $groupId, ParameterType::INTEGER)
                ->bind(':period', $period)
                ->bind(':count', $count, ParameterType::INTEGER)
                ->bind(':limit_value', $limitValue, ParameterType::INTEGER)
                ->bind(':period_start', $now)
                ->bind(':updated_at', $now);

            $this->db->setQuery($insert)->execute();
        }
    }

    public function getLimit(int $userId, string $period): int
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('limit_value'))
            ->from($this->db->quoteName('#__apofjdl_user_quotas'))
            ->where($this->db->quoteName('user_id') . ' = :user_id')
            ->where($this->db->quoteName('period') . ' = :period')
            ->bind(':user_id', $userId, ParameterType::INTEGER)
            ->bind(':period', $period);

        $result = $this->db->setQuery($query)->loadResult();

        return $result !== null ? (int) $result : 0;
    }
}
