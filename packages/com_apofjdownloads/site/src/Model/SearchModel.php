<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\QueryInterface;

/**
 * Search model for the site frontend.
 *
 * Full-text search across downloads.
 */
class SearchModel extends ListModel
{
    /**
     * Build the search query.
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('a.id'),
            $db->quoteName('a.title'),
            $db->quoteName('a.alias'),
            $db->quoteName('a.description'),
            $db->quoteName('a.catid'),
            $db->quoteName('a.hits'),
            $db->quoteName('a.created'),
            $db->quoteName('a.access'),
        ])
            ->from($db->quoteName('#__apofjdl_downloads', 'a'))
            ->where($db->quoteName('a.state') . ' = 1');

        // Join category title
        $query->select($db->quoteName('c.title', 'category_title'))
            ->join('LEFT', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'));

        // Subquery for total size
        $sizeSub = $db->getQuery(true)
            ->select('COALESCE(SUM(' . $db->quoteName('f.size') . '), 0)')
            ->from($db->quoteName('#__apofjdl_files', 'f'))
            ->where($db->quoteName('f.download_id') . ' = ' . $db->quoteName('a.id'))
            ->where($db->quoteName('f.state') . ' = 1');

        $query->select('(' . $sizeSub . ') AS ' . $db->quoteName('total_size'));

        // Search filter
        $searchword = $this->getState('filter.searchword');

        if (!empty($searchword)) {
            $search = '%' . trim($searchword) . '%';
            $query->where(
                '(' . $db->quoteName('a.title') . ' LIKE :s1'
                . ' OR ' . $db->quoteName('a.description') . ' LIKE :s2'
                . ' OR ' . $db->quoteName('a.alias') . ' LIKE :s3)',
            )
                ->bind(':s1', $search)
                ->bind(':s2', $search)
                ->bind(':s3', $search);
        }

        // Access filter
        $user = Factory::getApplication()->getIdentity();
        $groups = $user->getAuthorisedViewLevels();
        $query->whereIn($db->quoteName('a.access'), $groups);

        // Language filter
        $query->where(
            $db->quoteName('a.language') . ' IN (' . $db->quote('*') . ', ' . $db->quote(Factory::getApplication()->getLanguage()->getTag()) . ')',
        );

        $query->order($db->quoteName('a.title') . ' ASC');

        return $query;
    }

    /**
     * Populate state.
     */
    protected function populateState($ordering = 'a.title', $direction = 'ASC'): void
    {
        $app = Factory::getApplication();
        $searchword = $app->getInput()->getString('searchword', '');
        $this->setState('filter.searchword', $searchword);

        parent::populateState($ordering, $direction);
    }
}
