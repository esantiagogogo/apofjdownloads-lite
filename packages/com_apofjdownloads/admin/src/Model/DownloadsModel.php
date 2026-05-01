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
 * Downloads list model.
 */
class DownloadsModel extends ListModel
{
    /**
     * Constructor.
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias',
                'state', 'a.state',
                'access', 'a.access',
                'catid', 'a.catid',
                'category_title',
                'language', 'a.language',
                'featured', 'a.featured',
                'hits', 'a.hits',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'ordering', 'a.ordering',
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
            $db->quoteName('a.title'),
            $db->quoteName('a.alias'),
            $db->quoteName('a.state'),
            $db->quoteName('a.access'),
            $db->quoteName('a.catid'),
            $db->quoteName('a.language'),
            $db->quoteName('a.featured'),
            $db->quoteName('a.hits'),
            $db->quoteName('a.created'),
            $db->quoteName('a.created_by'),
            $db->quoteName('a.ordering'),
            $db->quoteName('a.checked_out'),
            $db->quoteName('a.checked_out_time'),
        ])
            ->from($db->quoteName('#__apofjdl_downloads', 'a'));

        // Join category
        $query->select($db->quoteName('c.title', 'category_title'))
            ->join('LEFT', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

        // Join access level
        $query->select($db->quoteName('ag.title', 'access_level'))
            ->join('LEFT', $db->quoteName('#__viewlevels', 'ag'), $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access'));

        // Join user (created_by)
        $query->select($db->quoteName('ua.name', 'author_name'))
            ->join('LEFT', $db->quoteName('#__users', 'ua'), $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by'));

        // Filter: state
        $state = $this->getState('filter.state');

        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = :state')
                ->bind(':state', $state, \Joomla\Database\ParameterType::INTEGER);
        } elseif ($state !== '*') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        // Filter: category
        $categoryId = $this->getState('filter.category_id');

        if (is_numeric($categoryId)) {
            $query->where($db->quoteName('a.catid') . ' = :catid')
                ->bind(':catid', $categoryId, \Joomla\Database\ParameterType::INTEGER);
        }

        // Filter: access
        $access = $this->getState('filter.access');

        if (is_numeric($access)) {
            $query->where($db->quoteName('a.access') . ' = :access')
                ->bind(':access', $access, \Joomla\Database\ParameterType::INTEGER);
        }

        // Filter: language
        $language = $this->getState('filter.language');

        if (!empty($language)) {
            $query->where($db->quoteName('a.language') . ' = :language')
                ->bind(':language', $language);
        }

        // Filter: featured
        $featured = $this->getState('filter.featured');

        if (is_numeric($featured)) {
            $query->where($db->quoteName('a.featured') . ' = :featured')
                ->bind(':featured', $featured, \Joomla\Database\ParameterType::INTEGER);
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
                $query->where('(' . $db->quoteName('a.title') . ' LIKE :search1 OR ' . $db->quoteName('a.alias') . ' LIKE :search2)')
                    ->bind(':search1', $search)
                    ->bind(':search2', $search);
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
