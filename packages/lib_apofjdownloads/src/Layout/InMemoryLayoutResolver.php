<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

/**
 * In-memory layout resolver for unit testing.
 */
class InMemoryLayoutResolver implements LayoutResolverInterface
{
    /** @var array<string, LayoutData> keyed by "{type}:{alias}" */
    private array $byAlias = [];

    /** @var array<int, LayoutData> keyed by ID */
    private array $byId = [];

    /** @var array<string, LayoutData> keyed by "{type}:category:{categoryId}" */
    private array $byCategory = [];

    /** @var array<string, LayoutData> keyed by "{type}:global" */
    private array $byGlobal = [];

    /** @var array<string, LayoutData> keyed by "{type}:download:{downloadId}" */
    private array $byDownload = [];

    public function addLayout(LayoutData $layout): void
    {
        $this->byId[$layout->id] = $layout;

        if ($layout->alias !== '') {
            $this->byAlias[$layout->type . ':' . $layout->alias] = $layout;
        }
    }

    public function addCategoryLayout(LayoutData $layout, int $categoryId): void
    {
        $this->byId[$layout->id] = $layout;
        $this->byCategory[$layout->type . ':category:' . $categoryId] = $layout;
    }

    public function addGlobalLayout(LayoutData $layout): void
    {
        $this->byId[$layout->id] = $layout;
        $this->byGlobal[$layout->type . ':global'] = $layout;
    }

    public function addDownloadLayout(LayoutData $layout, int $downloadId): void
    {
        $this->byId[$layout->id] = $layout;
        $this->byDownload[$layout->type . ':download:' . $downloadId] = $layout;
    }

    public function resolve(string $type, ?string $alias = null, ?int $categoryId = null): ?LayoutData
    {
        // Priority 1: explicit alias
        if ($alias !== null) {
            $key = $type . ':' . $alias;

            if (isset($this->byAlias[$key])) {
                return $this->byAlias[$key];
            }
        }

        // Priority 2: category scope
        if ($categoryId !== null) {
            $key = $type . ':category:' . $categoryId;

            if (isset($this->byCategory[$key])) {
                return $this->byCategory[$key];
            }
        }

        // Priority 3: global scope
        $key = $type . ':global';

        if (isset($this->byGlobal[$key])) {
            return $this->byGlobal[$key];
        }

        return null;
    }

    public function resolveById(int $id): ?LayoutData
    {
        return $this->byId[$id] ?? null;
    }

    public function resolveWithCascade(
        string $type,
        ?string $alias = null,
        ?int $downloadId = null,
        ?int $categoryId = null,
    ): ?LayoutData {
        // Priority 1: explicit alias
        if ($alias !== null) {
            $key = $type . ':' . $alias;

            if (isset($this->byAlias[$key])) {
                return $this->byAlias[$key];
            }
        }

        // Priority 2: download-level override
        if ($downloadId !== null) {
            $key = $type . ':download:' . $downloadId;

            if (isset($this->byDownload[$key])) {
                return $this->byDownload[$key];
            }
        }

        // Priority 3: category scope
        if ($categoryId !== null) {
            $key = $type . ':category:' . $categoryId;

            if (isset($this->byCategory[$key])) {
                return $this->byCategory[$key];
            }
        }

        // Priority 4: global scope
        $key = $type . ':global';

        if (isset($this->byGlobal[$key])) {
            return $this->byGlobal[$key];
        }

        return null;
    }
}
