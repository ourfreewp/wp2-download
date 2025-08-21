<?php

/**
 * PHPDoc parser for extracting component metadata.
 *
 * This class provides methods to scan PHP files for PHPDoc blocks and extract
 * relevant component metadata, including facets and relations.
 *
 * @package category WP2\Download\Modules\Archi\Parsers
 **/

namespace WP2\Download\Modules\Archi\Parsers;

use WP2\Download\Modules\Archi\Helpers;

/**
 * PHPDoc parser for extracting component metadata.
 *
 * @component_id archi_phpdoc
 * @namespace archi.parsers
 * @type Utility
 * @note "Parses PHPDoc blocks for SDK annotations."
 * @facet {"name": "register_components_from_phpdoc", "visibility": "public", "returnType": "void"}
 * @facet {"name": "get_php_files", "visibility": "private", "returnType": "array"}
 * @facet {"name": "parse_file_for_components", "visibility": "private", "returnType": "array"}
 * @facet {"name": "parse_component_block", "visibility": "private", "returnType": "?array"}
 * @relation {"to": "helpers", "type": "dependency", "label": "registers parsed components"}
 */
class PHPDoc
{
    /**
     * Scan PHP files in the given directory for PHPDoc component annotations and register them.
     *
     * @param string $directory
     */
    public static function register_components_from_phpdoc(string $directory): void
    {
        $files = self::get_php_files($directory);
        foreach ($files as $file) {
            $components = self::parse_file_for_components($file);
            foreach ($components as $component) {
                Helpers::register_annotation($component);
            }
        }
    }

    /**
     * Get all PHP files recursively in a directory.
     */
    private static function get_php_files(string $directory): array
    {
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        $files = [];
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            if (strtolower($file->getExtension()) === 'php') {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    /**
     * Parse a PHP file for PHPDoc blocks containing component annotations.
     * Returns an array of component arrays.
     */
    private static function parse_file_for_components(string $file): array
    {
        $contents = file_get_contents($file);
        $matches = [];
        // Match PHPDoc blocks with @component_id.
        preg_match_all('/\/\*\*([\s\S]*?)\*\//', $contents, $matches);
        $components = [];
        foreach ($matches[1] as $block) {
            if (strpos($block, '@component_id') !== false) {
                $component = self::parse_component_block($block);
                if ($component) {
                    error_log('DEBUG: PHPDoc parsed component: ' . wp_json_encode($component));
                    $components[] = $component;
                }
            }
        }
        error_log('DEBUG: PHPDoc parsed components array: ' . wp_json_encode($components));
        return $components;
    }

    /**
     * Parse a PHPDoc block and extract component metadata, including @facet, @relation, and @note lines.
     * Returns a component array or null.
     */
    private static function parse_component_block(string $block): ?array
    {
        $clean_block = self::clean_block_lines($block);
        $component = self::extract_fields($clean_block);
        $component['facets'] = self::extract_facets($clean_block);
        $component['relations'] = self::extract_relations($clean_block);
        $component['note'] = self::extract_notes($clean_block);
        return isset($component['component_id']) ? $component : null;
    }

    private static function clean_block_lines(string $block): string
    {
        $lines = explode("\n", $block);
        $clean_block = '';
        foreach ($lines as $line) {
            $clean_block .= preg_replace('/^\s*\*?\s?/', '', $line) . "\n";
        }
        return $clean_block;
    }

    private static function extract_fields(string $clean_block): array
    {
        $component = [];
        foreach ([
            'component_id',
            'namespace',
            'type',
            'title',
            'note',
            'url',
        ] as $field) {
            if (preg_match('/@' . $field . '\s+([^\n]*)/', $clean_block, $m)) {
                $component[$field] = trim($m[1]);
            }
        }
        return $component;
    }

    private static function extract_facets(string $clean_block): array
    {
        $facets = [];
        if (preg_match('/@facets\s+(\[.*?\])/ms', $clean_block, $m)) {
            $decoded = json_decode($m[1], true);
            if (is_array($decoded)) {
                $facets = array_merge($facets, $decoded);
            }
        }
        if (preg_match_all('/@facet\s+({.*?})/ms', $clean_block, $matches)) {
            foreach ($matches[1] as $json) {
                $facet = json_decode($json, true);
                if (is_array($facet)) {
                    $facets[] = $facet;
                }
            }
        }
        return $facets;
    }

    private static function extract_relations(string $clean_block): array
    {
        $relations = [];
        if (preg_match('/@relations\s+(\[.*?\])/ms', $clean_block, $m)) {
            $decoded = json_decode($m[1], true);
            if (is_array($decoded)) {
                $relations = array_merge($relations, $decoded);
            }
        }
        if (preg_match_all('/@relation\s+({.*?})/ms', $clean_block, $matches)) {
            foreach ($matches[1] as $json) {
                $relation = json_decode($json, true);
                if (is_array($relation)) {
                    $relations[] = $relation;
                }
            }
        }
        return $relations;
    }

    private static function extract_notes(string $clean_block): string
    {
        if (preg_match_all('/@note\s+"([^"]+)"/', $clean_block, $matches)) {
            return implode("\n", $matches[1]);
        }
        return '';
    }
}
