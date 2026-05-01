<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Component\ApofjDownloads\Site\Controller;

use Apotentia\Library\ApofjDownloads\Acl\PermissionChecker;
use Apotentia\Library\ApofjDownloads\Acl\QuotaManager;
use Apotentia\Library\ApofjDownloads\Acl\RateLimiter;
use Apotentia\Library\ApofjDownloads\Log\DownloadLogEntry;
use Apotentia\Library\ApofjDownloads\Log\DownloadLogger;
use Apotentia\Library\ApofjDownloads\Serve\FileSender;
use Apotentia\Library\ApofjDownloads\Token\TokenManager;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Site file download controller.
 *
 * Orchestrates the complete secure download flow:
 * token validation -> ACL check -> quota check -> rate limit -> log -> serve.
 */
class FileController extends BaseController
{
    private TokenManager $tokenManager;
    private PermissionChecker $permissionChecker;
    private QuotaManager $quotaManager;
    private RateLimiter $rateLimiter;
    private DownloadLogger $downloadLogger;
    private FileSender $fileSender;

    /**
     * Inject all required services.
     */
    public function setServices(
        TokenManager $tokenManager,
        PermissionChecker $permissionChecker,
        QuotaManager $quotaManager,
        RateLimiter $rateLimiter,
        DownloadLogger $downloadLogger,
        FileSender $fileSender,
    ): void {
        $this->tokenManager = $tokenManager;
        $this->permissionChecker = $permissionChecker;
        $this->quotaManager = $quotaManager;
        $this->rateLimiter = $rateLimiter;
        $this->downloadLogger = $downloadLogger;
        $this->fileSender = $fileSender;
    }

    /**
     * Handle file download request.
     *
     * Route: index.php?option=com_apofjdownloads&task=file.download&token={token}
     */
    public function download(): void
    {
        $app = $this->app;
        $user = $app->getIdentity();
        $input = $app->getInput();

        $tokenString = $input->getString('token', '');
        $userId = (int) $user->id;
        $ipAddress = $input->server->getString('REMOTE_ADDR', '0.0.0.0');
        $userAgent = $input->server->getString('HTTP_USER_AGENT', '');

        // 1. Validate token
        $tokenData = $this->tokenManager->validateToken($tokenString);

        if ($tokenData === null) {
            $this->downloadLogger->logDownload(0, 0, $userId, $ipAddress, $userAgent, DownloadLogEntry::TOKEN_INVALID);
            $this->deny(403, 'Invalid or expired download token.');

            return;
        }

        $fileId = $tokenData->fileId;

        // Load file record
        /** @var \Apotentia\Component\ApofjDownloads\Administrator\Table\FileTable $fileTable */
        $fileTable = $this->getModel('File', 'Administrator')->getTable('File', 'Administrator');
        $fileTable->load($fileId);

        if (!$fileTable->id) {
            $this->downloadLogger->logDownload(0, $fileId, $userId, $ipAddress, $userAgent, DownloadLogEntry::TOKEN_INVALID);
            $this->deny(404, 'File not found.');

            return;
        }

        $downloadId = (int) $fileTable->download_id;

        // 2. Check ACL permission
        if (!$this->permissionChecker->canDownload()) {
            $this->downloadLogger->logDownload($downloadId, $fileId, $userId, $ipAddress, $userAgent, DownloadLogEntry::DENIED_ACL);
            $this->deny(403, 'You do not have permission to download this file.');

            return;
        }

        // 3. Check quota
        $quotaStatus = $this->quotaManager->checkQuota($userId);

        if (!$quotaStatus->allowed) {
            $this->downloadLogger->logDownload($downloadId, $fileId, $userId, $ipAddress, $userAgent, DownloadLogEntry::DENIED_QUOTA);
            $this->deny(403, 'Download quota exceeded.');

            return;
        }

        // 4. Check rate limit
        $identifier = $userId > 0 ? 'user_' . $userId : 'ip_' . $ipAddress;

        if (!$this->rateLimiter->isAllowed($identifier)) {
            $this->downloadLogger->logDownload($downloadId, $fileId, $userId, $ipAddress, $userAgent, DownloadLogEntry::DENIED_RATE_LIMIT);
            $this->deny(429, 'Too many download requests. Please wait and try again.');

            return;
        }

        // 5. Consume the token (single-use)
        $this->tokenManager->consumeToken($tokenString);

        // 6. Record the request for rate limiting
        $this->rateLimiter->recordRequest($identifier);

        // 7. Log the successful download
        $this->downloadLogger->logDownload($downloadId, $fileId, $userId, $ipAddress, $userAgent, DownloadLogEntry::COMPLETED);

        // 8. Increment quota usage
        $this->quotaManager->incrementUsage($userId);

        // 9. Increment file download count
        $db = $fileTable->getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__apofjdl_files'))
            ->set($db->quoteName('download_count') . ' = ' . $db->quoteName('download_count') . ' + 1')
            ->where($db->quoteName('id') . ' = :fileId')
            ->bind(':fileId', $fileId, \Joomla\Database\ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        // 10. Determine send mode from component config
        $params = $app->getParams('com_apofjdownloads');
        $sendMode = (int) $params->get('enable_xsendfile', FileSender::MODE_PHP);

        // 11. Serve the file
        $this->fileSender->send(
            $fileTable->filepath,
            $fileTable->mime_type,
            $fileTable->filename,
            (int) $fileTable->size,
            $sendMode,
        );

        $app->close();
    }

    /**
     * Send an error response.
     */
    private function deny(int $statusCode, string $message): void
    {
        $this->app->setHeader('status', (string) $statusCode);
        $this->app->setHeader('Content-Type', 'text/plain; charset=utf-8');
        echo $message;
        $this->app->close();
    }
}
