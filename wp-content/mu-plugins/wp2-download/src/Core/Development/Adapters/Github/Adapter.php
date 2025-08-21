<?php

/**
 * Summary of namespace WP2\Download\Core\Development\Adapters\Github
 */

namespace WP2\Download\Core\Development\Adapters\Github;

use WP2\Download\Core\Development\ConnectionInterface;

/**
 * GitHub development adapter.
 *
 * @component_id development_github_adapter
 * @namespace development.adapters
 * @type Adapter
 * @note "GitHub development adapter."
 */
class Adapter implements ConnectionInterface
{
    protected $token;

    protected $api_base = 'https://api.github.com';

    protected $timeout = 12;

    public function __construct(array $config = [])
    {
        $this->token = (string) ($config['token'] ?? (defined('WP2_GITHUB_PAT') ? WP2_GITHUB_PAT : ''));
        $this->api_base = (string) ($config['api_base'] ?? 'https://api.github.com');
        if (isset($config['timeout'])) {
            $this->timeout = (int) $config['timeout'];
        }
    }

    public function connect(): bool
    {
        if ($this->token === '') {
            return false;
        }

        $url = rtrim($this->api_base, '/') . '/user';
        $args = [
            'method' => 'GET',
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => 'wp2-download (WordPress)',
            ],
        ];

        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            return false;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        return $code >= 200 && $code < 300;
    }
}
