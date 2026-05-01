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

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;

/**
 * Licenses table class.
 *
 * @since  1.0.0
 */
class LicenseTable extends Table
{
    /**
     * Object type alias.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'license';

    /**
     * Constructor.
     *
     * @param   DatabaseInterface  $db  Database connector object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__apofjdl_licenses', 'id', $db);
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
        if (trim($this->title ?? '') === '') {
            $this->setError('A license must have a title.');

            return false;
        }

        return true;
    }
}
