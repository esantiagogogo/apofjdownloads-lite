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

/**
 * Files list model.
 */
class FilesModel extends ListModel
{
    /**
     * Constructor.
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'filename', 'a.filename',
                'download_id', 'a.download_id',
                'download_title',
                'mime_type', 'a.mime_type',
                'state', 'a.state',
                'size', 'a.size',
                'download_count', 'a.download_count',
                'ordering', 'a.ordering',
                'created', 'a.created',
            ];
        }

        parent::__construct($config);
    }

    /**
     * Build the query for the list.
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('a.id'),
            $db->quoteName('a.download_id'),
            $db->quoteName('a.filename'),
            $db->quoteName('a.filepath'),
            $db->quoteName('a.storage_adapter'),
            $db->quoteName('a.size'),
            $db->quoteName('a.mime_type'),
            $db->quoteName('a.mime_verified'),
            $db->quoteName('a.hash_sha256'),
            $db->quoteName('a.hash_md5'),
            $db->quoteName('a.download_count'),
            $db->quoteName('a.ordering'),
            $db->quoteName('a.state'),
            $db->quoteName('a.created'),
        ])
            ->from($db->quoteName('#__apofjdl_files', 'a'));

        // Join download title
        $query->select($db->quoteName('d.title', 'download_title'))
            ->join('LEFT', $db->quoteName('#__apofjdl_downloads', 'd'), $db->quoteName('d.id') . ' = ' . $db->quoteName('a.download_id'));

        // Filter: state
        $state = $this->getState('filter.state');

        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = :state')
                ->bind(':state', $state, \Joomla\Database\ParameterType::INTEGER);
        } elseif ($state !== '*') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        // Filter: download_id
        $downloadId = $this->getState('filter.download_id');

        if (is_numeric($downloadId)) {
            $query->where($db->quoteName('a.download_id') . ' = :download_id')
                ->bind(':download_id', $downloadId, \Joomla\Database\ParameterType::INTEGER);
        }

        // Filter: mime_type
        $mimeType = $this->getState('filter.mime_type');

        if (!empty($mimeType)) {
            $query->where($db->quoteName('a.mime_type') . ' = :mime_type')
                ->bind(':mime_type', $mimeType);
        }

        // Filter: search
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $id = (int) substr($search, 3);
                $query->where($db->quoteName('a.id') . ' = :id')
                    ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);
            } else {
                $search = '%' . trim($search) . '%';
                $query->where($db->quoteName('a.filename') . ' LIKE :search')
                    ->bind(':search', $search);
            }
        }

        // Ordering
        $orderCol = $this->state->get('list.ordering', 'a.ordering');
        $orderDirn = $this->state->get('list.direction', 'ASC');

        $query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

        return $query;
    }

    /**
     * Populate state for filters.
     */
    protected function populateState($ordering = 'a.ordering', $direction = 'ASC'): void
    {
        parent::populateState($ordering, $direction);
    }
}
