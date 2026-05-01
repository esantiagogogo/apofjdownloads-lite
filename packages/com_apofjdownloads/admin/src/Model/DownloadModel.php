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
 * Download item model.
 */
class DownloadModel extends AdminModel
{
    /**
     * The type alias for this content type.
     *
     * @var string
     */
    public $typeAlias = 'com_apofjdownloads.download';

    /**
     * Get the form for the item.
     */
    public function getForm($data = [], $loadData = true): Form|false
    {
        $form = $this->loadForm(
            'com_apofjdownloads.download',
            'download',
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
            'com_apofjdownloads.edit.download.data',
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
        $date = Factory::getDate();
        $user = Factory::getApplication()->getIdentity();

        if (empty($table->id)) {
            $table->created = $date->toSql();
            $table->created_by = $user->id;

            if (empty($table->ordering)) {
                $db = $this->getDatabase();
                $query = $db->getQuery(true)
                    ->select('MAX(ordering)')
                    ->from('#__apofjdl_downloads');
                $db->setQuery($query);
                $max = (int) $db->loadResult();
                $table->ordering = $max + 1;
            }
        } else {
            $table->modified = $date->toSql();
            $table->modified_by = $user->id;
        }
    }

    /**
     * Publish/unpublish downloads.
     */
    public function publish(&$pks, $value = 1): bool
    {
        return parent::publish($pks, $value);
    }

    /**
     * Toggle featured state.
     */
    public function featured(array $pks, int $value = 0): bool
    {
        $table = $this->getTable();
        $db = $this->getDatabase();

        foreach ($pks as $pk) {
            $table->reset();
            $table->load($pk);
            $table->featured = $value;
            $table->store();
        }

        return true;
    }
}
