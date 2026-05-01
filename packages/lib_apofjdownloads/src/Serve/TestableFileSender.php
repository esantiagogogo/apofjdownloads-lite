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
 * Test double for FileSender that captures headers instead of sending them.
 *
 * Used only in unit tests — never in production.
 */
class TestableFileSender extends FileSender
{
    /** @var array<string> Captured headers */
    public array $headers = [];

    /** @var bool Whether sendPhp was called */
    public bool $phpStreamSent = false;

    /** @var bool Whether sendXsendfile was called */
    public bool $xsendfileSent = false;

    /** @var bool Whether sendXaccel was called */
    public bool $xaccelSent = false;

    protected function sendHeaders(string $mimeType, string $filename, int $size): void
    {
        $this->headers[] = 'Content-Type: ' . $mimeType;
        $this->headers[] = 'Content-Disposition: attachment; filename="' . $filename . '"';
        $this->headers[] = 'Content-Length: ' . $size;
        $this->headers[] = 'X-Content-Type-Options: nosniff';
        $this->headers[] = 'Cache-Control: private, no-cache, no-store';
    }

    protected function sendPhp(string $filepath): void
    {
        $this->phpStreamSent = true;
    }

    protected function sendXsendfile(string $filepath): void
    {
        $this->xsendfileSent = true;
        $this->headers[] = 'X-Sendfile: ' . $filepath;
    }

    protected function sendXaccel(string $filepath): void
    {
        $this->xaccelSent = true;
        $this->headers[] = 'X-Accel-Redirect: /protected-files/' . $filepath;
    }
}
