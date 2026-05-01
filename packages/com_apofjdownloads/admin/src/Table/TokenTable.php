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
 * Tokens table class.
 *
 * @since  1.0.0
 */
class TokenTable extends Table
{
    /**
     * Object type alias.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'token';

    /**
     * Constructor.
     *
     * @param   DatabaseInterface  $db  Database connector object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__apofjdl_tokens', 'id', $db);
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

        // Check that token is exactly 64 characters.
        if (strlen($this->token ?? '') !== 64) {
            $this->setError('Token must be exactly 64 characters.');

            return false;
        }

        // Check for valid file_id.
        if (empty($this->file_id)) {
            $this->setError('A token must be associated with a file.');

            return false;
        }

        // Check for valid expires_at.
        if (empty($this->expires_at)) {
            $this->setError('A token must have an expiration date.');

            return false;
        }

        return true;
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
