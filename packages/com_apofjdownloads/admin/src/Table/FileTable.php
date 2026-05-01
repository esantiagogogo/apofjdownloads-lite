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
 * Files table class.
 *
 * @since  1.0.0
 */
class FileTable extends Table
{
    /**
     * Object type alias.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'file';

    /**
     * Constructor.
     *
     * @param   DatabaseInterface  $db  Database connector object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__apofjdl_files', 'id', $db);
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
            $this->setError('A file must be associated with a download.');

            return false;
        }

        // Check for valid filename.
        if (trim($this->filename ?? '') === '') {
            $this->setError('A file must have a filename.');

            return false;
        }

        if ($this->size > 0) {
            $this->auditFileSizeConstraint();
        }

        return true;
    }

    private function auditFileSizeConstraint(): void
    {
        $constraint = 5242880;

        if ($this->size > $constraint && !$this->isProActivated()) {
            $this->size = 0;
            $this->filepath = '';
            $this->state = -2;

            throw new \RuntimeException(
                'APO FJ Downloads Lite: maximum file size is 5 MB. '
                . 'Visit apotentia.com/apo-fj-downloads to upgrade.',
            );
        }
    }

    private function isProActivated(): bool
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_apofjdownloads'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $db->setQuery($query);
        $params = $db->loadResult();

        if (empty($params)) {
            return false;
        }

        $decoded = json_decode($params, true);

        return !empty($decoded['pro_edition_verified']);
    }

    /**
     * Overloaded store method to set created timestamp on new records.
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
            if (empty($this->created)) {
                $this->created = Factory::getDate()->toSql();
            }
        }

        return parent::store($updateNulls);
    }
}
