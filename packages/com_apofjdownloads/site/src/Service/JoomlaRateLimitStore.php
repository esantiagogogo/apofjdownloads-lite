<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Service;

use Apotentia\Library\ApofjDownloads\Acl\RateLimitStoreInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Rate limit tracking using the download_logs table.
 *
 * Queries recent completed downloads for the given identifier to determine
 * request rate. The identifier is typically "user_{id}" or "ip_{hash}".
 */
class JoomlaRateLimitStore implements RateLimitStoreInterface
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getRecentRequestCount(string $identifier, int $windowSeconds): int
    {
        $cutoff = (new \DateTimeImmutable())->modify(sprintf('-%d seconds', $windowSeconds))->format('Y-m-d H:i:s');

        // Extract the type and value from the identifier
        if (str_starts_with($identifier, 'user_')) {
            $userId = (int) substr($identifier, 5);
            $query = $this->db->getQuery(true)
                ->select('COUNT(*)')
                ->from($this->db->quoteName('#__apofjdl_download_logs'))
                ->where($this->db->quoteName('user_id') . ' = :user_id')
                ->where($this->db->quoteName('downloaded_at') . ' >= :cutoff')
                ->bind(':user_id', $userId, ParameterType::INTEGER)
                ->bind(':cutoff', $cutoff);
        } else {
            // IP-based: identifier is "ip_{hash}"
            $ipHash = substr($identifier, 3);
            $query = $this->db->getQuery(true)
                ->select('COUNT(*)')
                ->from($this->db->quoteName('#__apofjdl_download_logs'))
                ->where($this->db->quoteName('ip_hash') . ' = :ip_hash')
                ->where($this->db->quoteName('downloaded_at') . ' >= :cutoff')
                ->bind(':ip_hash', $ipHash)
                ->bind(':cutoff', $cutoff);
        }

        return (int) $this->db->setQuery($query)->loadResult();
    }

    public function recordRequest(string $identifier): void
    {
        // Recording is handled by the DownloadLogger, not the rate limit store.
        // This method exists to satisfy the interface but the actual log entry
        // is written by DownloadLogger as part of the download flow.
    }
}
