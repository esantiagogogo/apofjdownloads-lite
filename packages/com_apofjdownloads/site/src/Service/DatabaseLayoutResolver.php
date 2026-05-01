<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Service;

use Apotentia\Library\ApofjDownloads\Layout\LayoutData;
use Apotentia\Library\ApofjDownloads\Layout\LayoutResolverInterface;
use Joomla\Database\DatabaseInterface;

/**
 * Database-backed layout resolver.
 *
 * Queries #__apofjdl_layouts to resolve layouts.
 */
class DatabaseLayoutResolver implements LayoutResolverInterface
{
    public function __construct(
        private readonly DatabaseInterface $db,
    ) {
    }

    public function resolve(string $type, ?string $alias = null, ?int $categoryId = null): ?LayoutData
    {
        // Priority 1: explicit alias
        if ($alias !== null) {
            $result = $this->queryByTypeAndAlias($type, $alias);

            if ($result !== null) {
                return $result;
            }
        }

        // Priority 2: category scope
        if ($categoryId !== null) {
            $result = $this->queryByScopeAndCategory($type, $categoryId);

            if ($result !== null) {
                return $result;
            }
        }

        // Priority 3: global scope
        return $this->queryGlobal($type);
    }

    public function resolveById(int $id): ?LayoutData
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__apofjdl_layouts'))
            ->where($this->db->quoteName('id') . ' = :id')
            ->where($this->db->quoteName('state') . ' = 1')
            ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);

        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        return $row ? $this->rowToLayoutData($row) : null;
    }

    public function resolveWithCascade(
        string $type,
        ?string $alias = null,
        ?int $downloadId = null,
        ?int $categoryId = null,
    ): ?LayoutData {
        // Priority 1: explicit alias
        if ($alias !== null) {
            $result = $this->queryByTypeAndAlias($type, $alias);

            if ($result !== null) {
                return $result;
            }
        }

        // Priority 2: download-level override (from download params)
        if ($downloadId !== null) {
            $result = $this->queryDownloadLayoutOverride($type, $downloadId);

            if ($result !== null) {
                return $result;
            }
        }

        // Priority 3: category scope
        if ($categoryId !== null) {
            $result = $this->queryByScopeAndCategory($type, $categoryId);

            if ($result !== null) {
                return $result;
            }
        }

        // Priority 4: global scope
        return $this->queryGlobal($type);
    }

    private function queryDownloadLayoutOverride(string $type, int $downloadId): ?LayoutData
    {
        $query = $this->db->getQuery(true)
            ->select($this->db->quoteName('params'))
            ->from($this->db->quoteName('#__apofjdl_downloads'))
            ->where($this->db->quoteName('id') . ' = :dlid')
            ->bind(':dlid', $downloadId, \Joomla\Database\ParameterType::INTEGER);

        $this->db->setQuery($query);
        $params = $this->db->loadResult();

        if ($params === null || $params === '') {
            return null;
        }

        $decoded = json_decode($params, true);
        $layoutAlias = $decoded['layout_override_' . $type] ?? null;

        if ($layoutAlias === null || $layoutAlias === '') {
            return null;
        }

        return $this->queryByTypeAndAlias($type, $layoutAlias);
    }

    private function queryByTypeAndAlias(string $type, string $alias): ?LayoutData
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__apofjdl_layouts'))
            ->where($this->db->quoteName('type') . ' = :type')
            ->where($this->db->quoteName('alias') . ' = :alias')
            ->where($this->db->quoteName('state') . ' = 1')
            ->bind(':type', $type)
            ->bind(':alias', $alias)
            ->setLimit(1);

        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        return $row ? $this->rowToLayoutData($row) : null;
    }

    private function queryByScopeAndCategory(string $type, int $categoryId): ?LayoutData
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__apofjdl_layouts'))
            ->where($this->db->quoteName('type') . ' = :type')
            ->where($this->db->quoteName('scope') . ' = ' . $this->db->quote('category'))
            ->where($this->db->quoteName('category_id') . ' = :catid')
            ->where($this->db->quoteName('state') . ' = 1')
            ->bind(':type', $type)
            ->bind(':catid', $categoryId, \Joomla\Database\ParameterType::INTEGER)
            ->setLimit(1);

        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        return $row ? $this->rowToLayoutData($row) : null;
    }

    private function queryGlobal(string $type): ?LayoutData
    {
        $query = $this->db->getQuery(true)
            ->select('*')
            ->from($this->db->quoteName('#__apofjdl_layouts'))
            ->where($this->db->quoteName('type') . ' = :type')
            ->where($this->db->quoteName('scope') . ' = ' . $this->db->quote('global'))
            ->where($this->db->quoteName('state') . ' = 1')
            ->bind(':type', $type)
            ->order($this->db->quoteName('ordering') . ' ASC')
            ->setLimit(1);

        $this->db->setQuery($query);
        $row = $this->db->loadObject();

        return $row ? $this->rowToLayoutData($row) : null;
    }

    private function rowToLayoutData(object $row): LayoutData
    {
        return new LayoutData(
            id: (int) $row->id,
            title: $row->title,
            alias: $row->alias,
            type: $row->type,
            bodyTwig: $row->body_twig,
            css: $row->css ?? '',
        );
    }
}
