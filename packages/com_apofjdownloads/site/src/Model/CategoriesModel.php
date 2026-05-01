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
 * Categories list model for the site frontend.
 *
 * Lists published categories with download counts.
 */
class CategoriesModel extends ListModel
{
    /**
     * Build the query for the categories list.
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select([
            $db->quoteName('c.id'),
            $db->quoteName('c.title'),
            $db->quoteName('c.alias'),
            $db->quoteName('c.description'),
            $db->quoteName('c.access'),
            $db->quoteName('c.language'),
            $db->quoteName('c.level'),
            $db->quoteName('c.parent_id'),
        ])
            ->from($db->quoteName('#__categories', 'c'))
            ->where($db->quoteName('c.extension') . ' = ' . $db->quote('com_apofjdownloads'))
            ->where($db->quoteName('c.published') . ' = 1');

        // Join download count
        $subQuery = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__apofjdl_downloads', 'd'))
            ->where($db->quoteName('d.catid') . ' = ' . $db->quoteName('c.id'))
            ->where($db->quoteName('d.state') . ' = 1');

        $query->select('(' . $subQuery . ') AS ' . $db->quoteName('download_count'));

        // Filter by access level
        $user = Factory::getApplication()->getIdentity();
        $groups = $user->getAuthorisedViewLevels();
        $query->whereIn($db->quoteName('c.access'), $groups);

        // Filter by language
        $query->where(
            $db->quoteName('c.language') . ' IN (' . $db->quote('*') . ', ' . $db->quote(Factory::getApplication()->getLanguage()->getTag()) . ')',
        );

        $query->order($db->quoteName('c.lft') . ' ASC');

        return $query;
    }
}
