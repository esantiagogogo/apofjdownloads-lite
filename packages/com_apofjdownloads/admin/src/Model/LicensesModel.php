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
 * Licenses list model.
 */
class LicensesModel extends ListModel
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
                'state', 'a.state',
                'require_agree', 'a.require_agree',
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
            $db->quoteName('a.state'),
            $db->quoteName('a.require_agree'),
            $db->quoteName('a.ordering'),
            $db->quoteName('a.checked_out'),
            $db->quoteName('a.checked_out_time'),
        ])
            ->from($db->quoteName('#__apofjdl_licenses', 'a'));

        // Filter: state
        $state = $this->getState('filter.state');

        if (is_numeric($state)) {
            $query->where($db->quoteName('a.state') . ' = :state')
                ->bind(':state', $state, \Joomla\Database\ParameterType::INTEGER);
        } elseif ($state !== '*') {
            $query->where($db->quoteName('a.state') . ' IN (0, 1)');
        }

        // Filter: require_agree
        $requireAgree = $this->getState('filter.require_agree');

        if (is_numeric($requireAgree)) {
            $query->where($db->quoteName('a.require_agree') . ' = :require_agree')
                ->bind(':require_agree', $requireAgree, \Joomla\Database\ParameterType::INTEGER);
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
                $query->where($db->quoteName('a.title') . ' LIKE :search')
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
