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
 * Category model for the site frontend.
 *
 * Loads a single category and its published downloads with pagination.
 */
class CategoryModel extends ListModel
{
    /**
     * The category object.
     */
    protected ?object $category = null;

    /**
     * Get the category data.
     */
    public function getCategory(): ?object
    {
        if ($this->category !== null) {
            return $this->category;
        }

        $id = $this->getState('category.id');

        if (!$id) {
            return null;
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('published') . ' = 1')
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_apofjdownloads'))
            ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);

        $db->setQuery($query);
        $this->category = $db->loadObject();

        return $this->category;
    }

    /**
     * Build the query for downloads in this category.
     */
    protected function getListQuery(): QueryInterface
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $categoryId = (int) $this->getState('category.id');

        $query->select([
            $db->quoteName('a.id'),
            $db->quoteName('a.title'),
            $db->quoteName('a.alias'),
            $db->quoteName('a.description'),
            $db->quoteName('a.hits'),
            $db->quoteName('a.created'),
            $db->quoteName('a.access'),
            $db->quoteName('a.language'),
        ])
            ->from($db->quoteName('#__apofjdl_downloads', 'a'))
            ->where($db->quoteName('a.catid') . ' = :catid')
            ->where($db->quoteName('a.state') . ' = 1')
            ->bind(':catid', $categoryId, \Joomla\Database\ParameterType::INTEGER);

        // Subquery for file count
        $fileCountSub = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__apofjdl_files', 'f'))
            ->where($db->quoteName('f.download_id') . ' = ' . $db->quoteName('a.id'))
            ->where($db->quoteName('f.state') . ' = 1');

        $query->select('(' . $fileCountSub . ') AS ' . $db->quoteName('file_count'));

        // Subquery for total size
        $sizeSub = $db->getQuery(true)
            ->select('COALESCE(SUM(' . $db->quoteName('f2.size') . '), 0)')
            ->from($db->quoteName('#__apofjdl_files', 'f2'))
            ->where($db->quoteName('f2.download_id') . ' = ' . $db->quoteName('a.id'))
            ->where($db->quoteName('f2.state') . ' = 1');

        $query->select('(' . $sizeSub . ') AS ' . $db->quoteName('total_size'));

        // Access filter
        $user = Factory::getApplication()->getIdentity();
        $groups = $user->getAuthorisedViewLevels();
        $query->whereIn($db->quoteName('a.access'), $groups);

        // Language filter
        $query->where(
            $db->quoteName('a.language') . ' IN (' . $db->quote('*') . ', ' . $db->quote(Factory::getApplication()->getLanguage()->getTag()) . ')',
        );

        $query->order($db->quoteName('a.ordering') . ' ASC');

        return $query;
    }

    /**
     * Populate state.
     */
    protected function populateState($ordering = 'a.ordering', $direction = 'ASC'): void
    {
        $app = Factory::getApplication();
        $id = $app->getInput()->getInt('id', 0);
        $this->setState('category.id', $id);

        parent::populateState($ordering, $direction);
    }
}
