<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Serve;

use Apotentia\Library\ApofjDownloads\Storage\StorageAdapterInterface;

/**
 * Sends files to the browser with proper security headers.
 *
 * Supports PHP streaming, Apache X-Sendfile, and Nginx X-Accel-Redirect.
 */
class FileSender
{
    public const MODE_PHP      = 0;
    public const MODE_XSENDFILE = 1;
    public const MODE_XACCEL   = 2;

    private StorageAdapterInterface $storage;

    public function __construct(StorageAdapterInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Send a file to the browser.
     *
     * @param string $filepath  Path within the storage adapter.
     * @param string $mimeType  Verified MIME type.
     * @param string $filename  Original filename for Content-Disposition.
     * @param int    $size      File size in bytes.
     * @param int    $sendMode  One of MODE_PHP, MODE_XSENDFILE, MODE_XACCEL.
     */
    public function send(
        string $filepath,
        string $mimeType,
        string $filename,
        int $size,
        int $sendMode = self::MODE_PHP,
    ): void {
        $this->sendHeaders($mimeType, $filename, $size);

        switch ($sendMode) {
            case self::MODE_XSENDFILE:
                $this->sendXsendfile($filepath);
                break;

            case self::MODE_XACCEL:
                $this->sendXaccel($filepath);
                break;

            default:
                $this->sendPhp($filepath);
                break;
        }
    }

    /**
     * Send security and content headers.
     */
    protected function sendHeaders(string $mimeType, string $filename, int $size): void
    {
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $this->sanitizeHeaderValue($filename) . '"');
        header('Content-Length: ' . $size);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-cache, no-store');
    }

    /**
     * Stream file via PHP (fpassthru).
     */
    protected function sendPhp(string $filepath): void
    {
        $stream = $this->storage->retrieve($filepath);
        fpassthru($stream);
        fclose($stream);
    }

    /**
     * Delegate to Apache mod_xsendfile.
     */
    protected function sendXsendfile(string $filepath): void
    {
        header('X-Sendfile: ' . $this->storage->getBasePath() . '/' . $filepath);
    }

    /**
     * Delegate to Nginx X-Accel-Redirect.
     */
    protected function sendXaccel(string $filepath): void
    {
        header('X-Accel-Redirect: /protected-files/' . $filepath);
    }

    /**
     * Sanitize a value for use in an HTTP header (strip newlines).
     */
    private function sanitizeHeaderValue(string $value): string
    {
        return str_replace(["\r", "\n", '"'], ['', '', "'"], $value);
    }
}
