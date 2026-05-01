<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * SEF Router for com_apofjdownloads.
 *
 * URL patterns:
 *   /downloads                              → categories view
 *   /downloads/{category-alias}             → category view
 *   /downloads/{category-alias}/{dl-alias}  → download detail
 *   /downloads/search                       → search view
 */
class Router extends RouterView
{
    private DatabaseInterface $db;

    public function __construct(SiteApplication $app, AbstractMenu $menu, DatabaseInterface $db)
    {
        $this->db = $db;

        // Configure view routes
        $categories = new RouterViewConfiguration('categories');
        $this->registerView($categories);

        $category = new RouterViewConfiguration('category');
        $category->setKey('id');
        $category->setParent($categories);
        $this->registerView($category);

        $download = new RouterViewConfiguration('download');
        $download->setKey('id');
        $download->setParent($category, 'catid');
        $this->registerView($download);

        $search = new RouterViewConfiguration('search');
        $this->registerView($search);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Get the segment(s) for a category.
     */
    public function getCategorySegment($id, $query): array
    {
        $alias = $this->getCategoryAlias((int) $id);

        return $alias ? [(int) $id => $alias] : [];
    }

    /**
     * Get the segment(s) for a download.
     */
    public function getDownloadSegment($id, $query): array
    {
        $alias = $this->getDownloadAlias((int) $id);

        return $alias ? [(int) $id => $alias] : [];
    }

    /**
     * Get the ID for a category segment.
     */
    public function getCategoryId($segment, $query): int
    {
        return $this->findCategoryIdByAlias($segment);
    }

    /**
     * Get the ID for a download segment.
     */
    public function getDownloadId($segment, $query): int
    {
        return $this->findDownloadIdByAlias($segment, (int) ($query['id'] ?? 0));
    }

    /**
     * Lookup category alias by ID.
     */
    private function getCategoryAlias(int $id): ?string
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('alias'))
            ->from($this->db->quoteName('#__categories'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $this->db->setQuery($query);

        return $this->db->loadResult() ?: null;
    }

    /**
     * Lookup download alias by ID.
     */
    private function getDownloadAlias(int $id): ?string
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('alias'))
            ->from($this->db->quoteName('#__apofjdl_downloads'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $this->db->setQuery($query);

        return $this->db->loadResult() ?: null;
    }

    /**
     * Find category ID by alias.
     */
    private function findCategoryIdByAlias(string $alias): int
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__categories'))
            ->where($this->db->quoteName('alias') . ' = :alias')
            ->where($this->db->quoteName('extension') . ' = ' . $this->db->quote('com_apofjdownloads'))
            ->bind(':alias', $alias);

        $this->db->setQuery($query);

        return (int) ($this->db->loadResult() ?? 0);
    }

    /**
     * Find download ID by alias, optionally scoped to a category.
     */
    private function findDownloadIdByAlias(string $alias, int $categoryId = 0): int
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('id'))
            ->from($this->db->quoteName('#__apofjdl_downloads'))
            ->where($this->db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $alias);

        if ($categoryId > 0) {
            $query->where($this->db->quoteName('catid') . ' = :catid')
                ->bind(':catid', $categoryId, ParameterType::INTEGER);
        }

        $this->db->setQuery($query);

        return (int) ($this->db->loadResult() ?? 0);
    }
}
