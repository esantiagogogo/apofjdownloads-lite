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
 * Layouts list model.
 */
class LayoutsModel extends ListModel
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
                'type', 'a.type',
                'scope', 'a.scope',
                'state', 'a.state',
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
            $db->quoteName('a.type'),
            $db->quoteName('a.scope'),
            $db->quoteName('a.category_id'),
            $db->quoteName('a.state'),
            $db->quoteName('a.ordering'),
        ])
            ->from($db->quoteName('#__apofjdl_layouts', 'a'));

        // Filter: state
        $state = $this->getState('filter.state');

        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = :state')
                ->bind(':state', $state, \Joomla\Database\ParameterType::INTEGER);
        } elseif ($state !== '*') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        // Filter: type
        $type = $this->getState('filter.type');

        if (!empty($type)) {
            $query->where($db->quoteName('a.type') . ' = :type')
                ->bind(':type', $type);
        }

        // Filter: scope
        $scope = $this->getState('filter.scope');

        if (!empty($scope)) {
            $query->where($db->quoteName('a.scope') . ' = :scope')
                ->bind(':scope', $scope);
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
