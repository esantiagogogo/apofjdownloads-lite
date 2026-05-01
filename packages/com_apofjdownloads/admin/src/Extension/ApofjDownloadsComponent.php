<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Administrator\Extension;

use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Component class for com_apofjdownloads.
 */
class ApofjDownloadsComponent extends MVCComponent implements
    BootableExtensionInterface,
    CategoryServiceInterface
{
    use CategoryServiceTrait;
    use HTMLRegistryAwareTrait;

    /**
     * Booting the extension. Called after all services are registered.
     *
     * Stores the container reference for lazy-loading the LayoutEngine
     * when site frontend views are dispatched.
     */
    public function boot(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * The DI container (set during boot).
     */
    private ?ContainerInterface $container = null;

    /**
     * Get the DI container.
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * Returns the table name for the count of items in a category.
     */
    public function getTableNameForSection(string $section = null): string
    {
        return '#__apofjdl_downloads';
    }

    /**
     * Returns the state column for the count query.
     */
    protected function getStateColumnForSection(string $section = null): string
    {
        return 'state';
    }
}
