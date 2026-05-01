<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

class LogsModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'download_id', 'a.download_id',
                'download_title',
                'file_id', 'a.file_id',
                'user_id', 'a.user_id',
                'username',
                'status', 'a.status',
                'downloaded_at', 'a.downloaded_at',
            ];
        }

        parent::__construct($config);
    }

    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('a.id'),
            $db->quoteName('a.download_id'),
            $db->quoteName('a.file_id'),
            $db->quoteName('a.user_id'),
            $db->quoteName('a.ip_hash'),
            $db->quoteName('a.user_agent'),
            $db->quoteName('a.downloaded_at'),
            $db->quoteName('a.status'),
        ])
            ->from($db->quoteName('#__apofjdl_download_logs', 'a'));

        // Join download title
        $query->select($db->quoteName('d.title', 'download_title'))
            ->join('LEFT', $db->quoteName('#__apofjdl_downloads', 'd'), $db->quoteName('d.id') . ' = ' . $db->quoteName('a.download_id'));

        // Join username
        $query->select($db->quoteName('u.username', 'username'))
            ->join('LEFT', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('a.user_id'));

        // Filter: download_id
        $downloadId = $this->getState('filter.download_id');

        if (is_numeric($downloadId)) {
            $query->where($db->quoteName('a.download_id') . ' = :download_id')
                ->bind(':download_id', $downloadId, \Joomla\Database\ParameterType::INTEGER);
        }

        // Filter: status
        $status = $this->getState('filter.status');

        if (!empty($status)) {
            $query->where($db->quoteName('a.status') . ' = :status')
                ->bind(':status', $status);
        }

        // Filter: search (by username or IP hash prefix)
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $id = (int) substr($search, 3);
                $query->where($db->quoteName('a.id') . ' = :id')
                    ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);
            } else {
                $search = '%' . trim($search) . '%';
                $query->where(
                    '(' . $db->quoteName('u.username') . ' LIKE :search'
                    . ' OR ' . $db->quoteName('a.ip_hash') . ' LIKE :search2)',
                )
                    ->bind(':search', $search)
                    ->bind(':search2', $search);
            }
        }

        // Ordering
        $orderCol = $this->state->get('list.ordering', 'a.downloaded_at');
        $orderDirn = $this->state->get('list.direction', 'DESC');
        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    protected function populateState($ordering = 'a.downloaded_at', $direction = 'DESC'): void
    {
        parent::populateState($ordering, $direction);
    }
}
