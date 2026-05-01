<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\View\Search;

use Apotentia\Library\ApofjDownloads\Layout\LayoutEngine;
use Apotentia\Library\ApofjDownloads\Layout\LayoutType;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Search results view for the site frontend.
 */
class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected string $searchword = '';
    protected $params;
    public string $renderedOutput = '';
    private ?LayoutEngine $layoutEngine = null;

    public function setLayoutEngine(LayoutEngine $engine): void
    {
        $this->layoutEngine = $engine;
    }

    public function display($tpl = null): void
    {
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->searchword = Factory::getApplication()->getInput()->getString('searchword', '');

        if ($this->layoutEngine !== null) {
            $this->renderedOutput = $this->layoutEngine->render(
                LayoutType::SEARCH_RESULTS,
                [
                    'downloads' => $this->items ?? [],
                    'searchword' => $this->searchword,
                    'pagination' => $this->pagination ? $this->pagination->getListFooter() : '',
                ],
            );
        }

        parent::display($tpl);
    }
}
