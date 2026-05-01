<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Download detail model for the site frontend.
 *
 * Loads a single download with its files, category, and license.
 */
class DownloadModel extends BaseDatabaseModel
{
    /**
     * Get a single download item with related data.
     */
    public function getItem(?int $id = null): ?object
    {
        if ($id === null) {
            $id = Factory::getApplication()->getInput()->getInt('id', 0);
        }

        if (!$id) {
            return null;
        }

        $db = $this->getDatabase();

        // Load download with category and license join
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('a') . '.*',
                $db->quoteName('c.title', 'category_title'),
                $db->quoteName('c.alias', 'category_alias'),
                $db->quoteName('l.title', 'license_title'),
                $db->quoteName('l.body', 'license_body'),
                $db->quoteName('l.require_agree', 'license_require_agree'),
            ])
            ->from($db->quoteName('#__apofjdl_downloads', 'a'))
            ->join('LEFT', $db->quoteName('#__categories', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('a.catid'))
            ->join('LEFT', $db->quoteName('#__apofjdl_licenses', 'l'), $db->quoteName('l.id') . ' = ' . $db->quoteName('a.license_id'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->where($db->quoteName('a.state') . ' = 1')
            ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);

        // Access filter
        $user = Factory::getApplication()->getIdentity();
        $groups = $user->getAuthorisedViewLevels();
        $query->whereIn($db->quoteName('a.access'), $groups);

        $db->setQuery($query);
        $item = $db->loadObject();

        return $item ?: null;
    }

    /**
     * Get files for a download.
     */
    public function getFiles(int $downloadId): array
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__apofjdl_files'))
            ->where($db->quoteName('download_id') . ' = :dlid')
            ->where($db->quoteName('state') . ' = 1')
            ->bind(':dlid', $downloadId, \Joomla\Database\ParameterType::INTEGER)
            ->order($db->quoteName('ordering') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    /**
     * Increment the hit counter for a download.
     */
    public function hit(int $id): void
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__apofjdl_downloads'))
            ->set($db->quoteName('hits') . ' = ' . $db->quoteName('hits') . ' + 1')
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);

        $db->setQuery($query);
        $db->execute();
    }
}
