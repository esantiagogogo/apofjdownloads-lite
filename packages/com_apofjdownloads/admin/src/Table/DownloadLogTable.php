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

/**
 * Download logs table class.
 *
 * @since  1.0.0
 */
class DownloadLogTable extends Table
{
    /**
     * Object type alias.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'downloadlog';

    /**
     * Constructor.
     *
     * @param   DatabaseInterface  $db  Database connector object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__apofjdl_download_logs', 'id', $db);
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

        // Check for valid download_id.
        if (empty($this->download_id)) {
            $this->setError('A download log entry must reference a download.');

            return false;
        }

        // Check for valid file_id.
        if (empty($this->file_id)) {
            $this->setError('A download log entry must reference a file.');

            return false;
        }

        return true;
    }

    /**
     * Overloaded store method to set downloaded_at timestamp on new records.
     *
     * @param   boolean  $updateNulls  True to update fields even if they are null.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    public function store($updateNulls = false): bool
    {
        if (empty($this->id)) {
            if (empty($this->downloaded_at)) {
                $this->downloaded_at = Factory::getDate()->toSql();
            }
        }

        return parent::store($updateNulls);
    }
}
