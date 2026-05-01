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
 * User quotas table class.
 *
 * @since  1.0.0
 */
class UserQuotaTable extends Table
{
    /**
     * Object type alias.
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'userquota';

    /**
     * Constructor.
     *
     * @param   DatabaseInterface  $db  Database connector object.
     *
     * @since   1.0.0
     */
    public function __construct(DatabaseInterface $db)
    {
        parent::__construct('#__apofjdl_user_quotas', 'id', $db);
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

        // Check that at least user_id or group_id is set.
        if (empty($this->user_id) && empty($this->group_id)) {
            $this->setError('A user quota must be associated with a user or a group.');

            return false;
        }

        return true;
    }
}
