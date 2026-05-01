<?php

/**
 * @package     Apotentia.ApofjDownloads
 * @subpackage  Administrator
 *
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Apotentia\Component\ApofjDownloads\Administrator\Table;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Filter\OutputFilter;

/**
 * Downloads table class.
 *
 * @since  1.0.0
 */
class DownloadTable extends Table
{
    /**
     * Object type alias.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'download';

    /**
     * Constructor.
     *
     * @param   DatabaseInterface  $db  Database connector object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__apofjdl_downloads', 'id', $db);
    }

    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function check(): bool
    {
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }

        // Check for valid title.
        if (trim($this->title) === '') {
            $this->setError('A download must have a title.');

            return false;
        }

        // Generate alias from title if alias is empty.
        if (trim($this->alias) === '') {
            $this->alias = $this->title;
        }

        $this->alias = OutputFilter::stringURLSafe($this->alias);

        // Check alias uniqueness.
        $db    = $this->_db;
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__apofjdl_downloads'))
            ->where($db->quoteName('alias') . ' = :alias')
            ->bind(':alias', $this->alias);

        if ($this->id) {
            $query->where($db->quoteName('id') . ' != :id')
                ->bind(':id', $this->id, \Joomla\Database\ParameterType::INTEGER);
        }

        $db->setQuery($query);

        if ((int) $db->loadResult() > 0) {
            $this->setError('Another download with this alias already exists.');

            return false;
        }

        return true;
    }

    /**
     * Overloaded store method to set created/modified timestamps.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function store($updateNulls = false): bool
    {
        $date = Factory::getDate()->toSql();
        $user = Factory::getApplication()->getIdentity();

        if (empty($this->id)) {
            // New record.
            if (empty($this->created)) {
                $this->created = $date;
            }

            if (empty($this->created_by)) {
                $this->created_by = $user->id;
            }
        } else {
            // Existing record.
            $this->modified    = $date;
            $this->modified_by = $user->id;
        }

        return parent::store($updateNulls);
    }
}
