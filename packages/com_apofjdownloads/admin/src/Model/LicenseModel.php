<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * License item model.
 */
class LicenseModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var string
     */
    public $typeAlias = 'com_apofjdownloads.license';

    /**
     * Get the form for the item.
     */
    public function getForm($data = [], $loadData = true): Form|false
    {
        $form = $this->loadForm(
            'com_apofjdownloads.license',
            'license',
            ['control' => 'jform', 'load_data' => $loadData],
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Load the data for the form.
     */
    protected function loadFormData(): mixed
    {
        $data = Factory::getApplication()->getUserState(
            'com_apofjdownloads.edit.license.data',
            [],
        );

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     */
    protected function prepareTable($table): void
    {
        if (empty($table->id)) {
            if (empty($table->ordering)) {
                $db = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from('#__apofjdl_licenses');
                $db->setQuery($query);
                $max = (int) $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    /**
     * Publish/unpublish licenses.
     */
    public function publish(&$pks, $value = 1): bool
    {
        return parent::publish($pks, $value);
    }
}
