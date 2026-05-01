<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Custom Twig functions for APO FJ Downloads templates.
 */
class TwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('format_size', [$this, 'formatSize']),
            new TwigFunction('format_date', [$this, 'formatDate']),
        ];
    }

    /**
     * Format bytes into a human-readable size string.
     */
    public function formatSize(int|float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max(0, (float) $bytes);

        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $power = min($power, \count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return round($value, 2) . ' ' . $units[$power];
    }

    /**
     * Format a date string.
     */
    public function formatDate(string $date, string $format = 'Y-m-d'): string
    {
        $dt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date);

        if ($dt === false) {
            $dt = new \DateTimeImmutable($date);
        }

        return $dt->format($format);
    }
}
