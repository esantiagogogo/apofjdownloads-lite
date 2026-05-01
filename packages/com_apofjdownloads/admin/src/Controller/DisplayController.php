<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Administrator\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Default display controller for the admin component.
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var string
     */
    protected $default_view = 'downloads';

    /**
     * Display the view.
     */
    public function display($cachable = false, $urlparams = []): static
    {
        return parent::display($cachable, $urlparams);
    }
}
