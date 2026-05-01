<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Acl;

/**
 * Checks ACL permissions for APO FJ Downloads actions.
 *
 * Takes a Closure for ACL lookups, isolating from Joomla's User::authorise().
 * This makes the permission logic unit-testable without Joomla CMS.
 */
class PermissionChecker
{
    /** @var \Closure(string, ?int): bool */
    private \Closure $aclLookup;

    /**
     * @param \Closure $aclLookup Signature: function(string $action, ?int $assetId): bool
     */
    public function __construct(\Closure $aclLookup)
    {
        $this->aclLookup = $aclLookup;
    }

    /**
     * Check if the user can download files.
     */
    public function canDownload(?int $assetId = null): bool
    {
        return ($this->aclLookup)('apofjdl.download', $assetId);
    }

    /**
     * Check if the user can upload files (admin).
     */
    public function canUpload(?int $assetId = null): bool
    {
        return ($this->aclLookup)('apofjdl.upload', $assetId);
    }

    /**
     * Check if the user can upload files (frontend).
     */
    public function canUploadFrontend(?int $assetId = null): bool
    {
        return ($this->aclLookup)('apofjdl.upload.frontend', $assetId);
    }

    /**
     * Check if the user can view download logs.
     */
    public function canViewLogs(): bool
    {
        return ($this->aclLookup)('apofjdl.view.logs', null);
    }

    /**
     * Check if the user can manage layouts.
     */
    public function canManageLayouts(): bool
    {
        return ($this->aclLookup)('apofjdl.manage.layouts', null);
    }
}
