<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Service;

use Apotentia\Library\ApofjDownloads\Log\DownloadLogEntry;
use Apotentia\Library\ApofjDownloads\Log\DownloadLogStoreInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Joomla database-backed download log store using #__apofjdl_download_logs.
 */
class JoomlaDownloadLogStore implements DownloadLogStoreInterface
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function log(DownloadLogEntry $entry): bool
    {
        $downloadId = $entry->downloadId;
        $fileId = $entry->fileId;
        $userId = $entry->userId;
        $ipHash = $entry->ipHash;
        $userAgent = $entry->userAgent;
        $downloadedAt = $entry->downloadedAt->format('Y-m-d H:i:s');
        $status = $entry->status;

        $query = $this->db->getQuery(true)
            ->insert($this->db->quoteName('#__apofjdl_download_logs'))
            ->columns([
                $this->db->quoteName('download_id'),
                $this->db->quoteName('file_id'),
                $this->db->quoteName('user_id'),
                $this->db->quoteName('ip_hash'),
                $this->db->quoteName('user_agent'),
                $this->db->quoteName('downloaded_at'),
                $this->db->quoteName('status'),
            ])
            ->values(':download_id, :file_id, :user_id, :ip_hash, :user_agent, :downloaded_at, :status')
            ->bind(':download_id', $downloadId, ParameterType::INTEGER)
            ->bind(':file_id', $fileId, ParameterType::INTEGER)
            ->bind(':user_id', $userId, ParameterType::INTEGER)
            ->bind(':ip_hash', $ipHash)
            ->bind(':user_agent', $userAgent)
            ->bind(':downloaded_at', $downloadedAt)
            ->bind(':status', $status);

        $this->db->setQuery($query)->execute();

        return true;
    }
}
