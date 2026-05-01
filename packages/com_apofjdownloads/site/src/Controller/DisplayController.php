<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Default display controller for the site frontend.
 */
class DisplayController extends BaseController
{
    /**
     * Default view name.
     *
     * @var string
     */
    protected $default_view = 'categories';

    /**
     * Display the requested view.
     */
    public function display($cachable = false, $urlparams = []): static
    {
        return parent::display($cachable, $urlparams);
    }
}
