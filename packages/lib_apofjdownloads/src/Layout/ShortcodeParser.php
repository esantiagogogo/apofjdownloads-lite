<?php

declare(strict_types=1);

/**
 * @package     APO FJ Downloads
 * @copyright   Copyright (C) 2026 Apotentia LLC. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Apotentia\Library\ApofjDownloads\Layout;

/**
 * Parses {apofjdl ...} shortcodes from text.
 *
 * Pure PHP — no Joomla dependency.
 */
class ShortcodeParser
{
    private const PATTERN = '/\{apofjdl\s+([^}]+)\}/';

    /**
     * Parse all shortcodes from the given text.
     *
     * @return ShortcodeMatch[]
     */
    public function parse(string $text): array
    {
        if (!preg_match_all(self::PATTERN, $text, $matches, \PREG_OFFSET_CAPTURE)) {
            return [];
        }

        $results = [];

        foreach ($matches[0] as $i => $match) {
            $fullMatch = $match[0];
            $offset = $match[1];
            $paramsString = $matches[1][$i][0];

            $params = $this->parseParams($paramsString);
            $shortcode = $this->buildShortcodeMatch($fullMatch, $offset, $params);

            if ($shortcode !== null) {
                $results[] = $shortcode;
            }
        }

        return $results;
    }

    /**
     * Parse key=value pairs from shortcode content.
     *
     * @return array<string, string>
     */
    private function parseParams(string $input): array
    {
        $params = [];

        // Match key=value, key="value with spaces", key='value'
        preg_match_all('/(\w+)=(?:"([^"]*?)"|\'([^\']*?)\'|(\S+))/', $input, $paramMatches);

        foreach ($paramMatches[1] as $i => $key) {
            $value = $paramMatches[2][$i] !== '' ? $paramMatches[2][$i]
                : ($paramMatches[3][$i] !== '' ? $paramMatches[3][$i]
                    : $paramMatches[4][$i]);

            $params[strtolower($key)] = $value;
        }

        return $params;
    }

    /**
     * Build a ShortcodeMatch from parsed parameters.
     */
    private function buildShortcodeMatch(string $fullMatch, int $offset, array $params): ?ShortcodeMatch
    {
        $layoutAlias = $params['layout'] ?? null;
        $limit = isset($params['limit']) ? (int) $params['limit'] : null;

        // Determine shortcode type
        if (isset($params['search']) && $params['search'] === 'true') {
            return new ShortcodeMatch(
                type: ShortcodeMatch::TYPE_SEARCH,
                fullMatch: $fullMatch,
                offset: $offset,
                layoutAlias: $layoutAlias,
                limit: $limit,
            );
        }

        if (isset($params['category'])) {
            return new ShortcodeMatch(
                type: ShortcodeMatch::TYPE_CATEGORY,
                fullMatch: $fullMatch,
                offset: $offset,
                categoryId: (int) $params['category'],
                layoutAlias: $layoutAlias,
                limit: $limit,
            );
        }

        if (isset($params['id'])) {
            return new ShortcodeMatch(
                type: ShortcodeMatch::TYPE_DOWNLOAD,
                fullMatch: $fullMatch,
                offset: $offset,
                id: (int) $params['id'],
                layoutAlias: $layoutAlias,
                limit: $limit,
            );
        }

        return null;
    }
}
