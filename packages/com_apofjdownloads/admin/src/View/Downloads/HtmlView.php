<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Administrator\View\Downloads;

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Downloads list view.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * An array of items.
     *
     * @var array
     */
    protected $items;

    /**
     * The pagination object.
     *
     * @var \Joomla\CMS\Pagination\Pagination
     */
    protected $pagination;

    /**
     * The model state.
     *
     * @var \Joomla\Registry\Registry
     */
    protected $state;

    /**
     * Form object for filters.
     *
     * @var \Joomla\CMS\Form\Form
     */
    public $filterForm;

    /**
     * Active filters.
     *
     * @var array
     */
    public $activeFilters;

    /**
     * Display the view.
     */
    public function display($tpl = null): void
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (\count($errors = $this->get('Errors'))) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     */
    protected function addToolbar(): void
    {
        $canDo = ContentHelper::getActions('com_apofjdownloads');

        ToolbarHelper::title(Text::_('COM_APOFJDOWNLOADS_DOWNLOADS'), 'download');

        $toolbar = Toolbar::getInstance('toolbar');

        if ($canDo->get('core.create')) {
            $toolbar->addNew('download.add');
        }

        if ($canDo->get('core.edit.state')) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            $childBar->publish('downloads.publish')->listCheck(true);
            $childBar->unpublish('downloads.unpublish')->listCheck(true);
            $childBar->standardButton('featured', 'JFEATURE', 'downloads.featured')->listCheck(true);
            $childBar->standardButton('unfeatured', 'JUNFEATURE', 'downloads.unfeatured')->listCheck(true);
            $childBar->archive('downloads.archive')->listCheck(true);

            if ($this->state->get('filter.state') != -2) {
                $childBar->trash('downloads.trash')->listCheck(true);
            }
        }

        if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
            $toolbar->delete('downloads.delete')
                ->text('JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($canDo->get('core.admin') || $canDo->get('core.options')) {
            $toolbar->preferences('com_apofjdownloads');
        }
    }
}
