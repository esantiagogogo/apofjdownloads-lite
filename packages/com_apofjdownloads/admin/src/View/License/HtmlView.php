<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Administrator\View\License;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * License edit view.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The form object.
     *
     * @var \Joomla\CMS\Form\Form
     */
    protected $form;

    /**
     * The item being edited.
     *
     * @var object
     */
    protected $item;

    /**
     * The model state.
     *
     * @var \Joomla\Registry\Registry
     */
    protected $state;

    /**
     * Display the view.
     */
    public function display($tpl = null): void
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

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
        Factory::getApplication()->getInput()->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        $canDo = ContentHelper::getActions('com_apofjdownloads');

        $title = $isNew
            ? Text::_('COM_APOFJDOWNLOADS_LICENSE_NEW')
            : Text::_('COM_APOFJDOWNLOADS_LICENSE_EDIT');

        ToolbarHelper::title($title, 'file-contract');

        $canSave = $canDo->get('core.create') || $canDo->get('core.edit') || $canDo->get('core.edit.own');

        if ($canSave) {
            ToolbarHelper::apply('license.apply');
            ToolbarHelper::save('license.save');
            ToolbarHelper::save2new('license.save2new');
        }

        ToolbarHelper::cancel('license.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
