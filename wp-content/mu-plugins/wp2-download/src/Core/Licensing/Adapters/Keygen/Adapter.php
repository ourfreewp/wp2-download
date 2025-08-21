<?php

/**
 * Keygen licensing adapter.
 */

namespace WP2\Download\Core\Licensing\Adapters\Keygen;

use WP2\Download\Core\Licensing\ConnectionInterface;

/**
 * Keygen licensing adapter.
 *
 * @component_id licensing_keygen_adapter
 * @namespace licensing.adapters
 * @type Adapter
 * @note "Keygen licensing adapter."
 */
class Adapter implements ConnectionInterface
{
    protected $account;

    protected $product;

    protected $policy;

    protected $token;

    protected $env;
    protected $api_base;

    protected $timeout = 15;

    /**
     * Constructor.
     *
     * @param array<string,mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->account = $this->get_config_value($config, 'account', 'WP2_KEYGEN_ACCOUNT');
        $this->product = $this->get_config_value($config, 'product', 'WP2_KEYGEN_PRODUCT');
        $this->policy = $this->get_config_value($config, 'policy', 'WP2_KEYGEN_POLICY');
        $this->token = $this->get_config_value($config, 'token', 'WP2_KEYGEN_TOKEN');
        $this->env = $this->get_config_value($config, 'env', 'WP2_KEYGEN_ENV');

        $derived_base = $this->account !== '' ? 'https://api.keygen.sh/v1/accounts/' . rawurlencode($this->account) : '';
        $this->api_base = $this->get_config_value($config, 'api_base', 'WP2_KEYGEN_API_BASE', $derived_base);

        if (isset($config['timeout'])) {
            $this->timeout = (int) $config['timeout'];
        }
    }

    /**
     * Helper to get config value, falling back to constant or default.
     *
     * @param array<string,mixed> $config
     * @param string $key
     * @param string $constant
     * @param mixed $default
     * @return string
     */
    protected function get_config_value(array $config, string $key, string $constant, $default = ''): string
    {
        if (isset($config[$key])) {
            return (string) $config[$key];
        }
        if (defined($constant)) {
            return (string) constant($constant);
        }
        return (string) $default;
    }

    /** {@inheritDoc} */
    public function connect(): bool
    {
        return $this->account !== '' && $this->token !== '' && $this->api_base !== '';
    }

    /**
     * Validate a license key.
     *
     * @param string $license_key
     * @return array{ok:bool,status:int,data:array<string,mixed>|null,error:string|null}
     */
    public function validate_license_key(string $license_key): array
    {
        $payload = [
            'meta' => [
                'key' => $license_key,
                'environment' => $this->env,
                'product' => $this->product ? $this->product : null,
                'policy' => $this->policy ? $this->policy : null,
            ],
        ];
        return $this->request('POST', '/licenses/actions/validate-key', $payload);
    }

    /**
     * Activate a machine install for a license.
     *
     * @param string $license_id
     * @param string $fingerprint Unique machine fingerprint.
     * @return array{ok:bool,status:int,data:array<string,mixed>|null,error:string|null}
     */
    public function activate_install(string $license_id, string $fingerprint): array
    {
        $payload = [
            'meta' => ['fingerprint' => $fingerprint],
        ];
        return $this->request('POST', '/licenses/' . rawurlencode($license_id) . '/actions/activate', $payload);
    }

    /**
     * Deactivate a machine install for a license.
     *
     * @param string $license_id
     * @param string $fingerprint
     * @return array{ok:bool,status:int,data:array<string,mixed>|null,error:string|null}
     */
    public function deactivate_install(string $license_id, string $fingerprint): array
    {
        $payload = [
            'meta' => ['fingerprint' => $fingerprint],
        ];
        return $this->request('POST', '/licenses/' . rawurlencode($license_id) . '/actions/deactivate', $payload);
    }

    /**
     * Retrieve a license by key.
     *
     * @param string $license_key
     * @return array{ok:bool,status:int,data:array<string,mixed>|null,error:string|null}
     */
    public function get_license_by_key(string $license_key): array
    {
        $path = '/licenses?filter[key]=' . rawurlencode($license_key);
        return $this->request('GET', $path);
    }

    /**
     * Low-level HTTP wrapper for Keygen API.
     *
     * @param string                   $method GET|POST|PATCH|DELETE
     * @param string                   $path   Path starting with '/'
     * @param array<string,mixed>|null $body   Optional JSON body
     * @return array{ok:bool,status:int,data:array<string,mixed>|null,error:string|null}
     */
    protected function request(string $method, string $path, array $body = null): array
    {
        if (!$this->connect()) {
            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => 'keygen_not_configured',
            ];
        }

        $url = rtrim($this->api_base, '/') . $path;
        $args = [
            'method' => $method,
            'timeout' => $this->timeout,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ];
        if ($body !== null) {
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            return [
                'ok' => false,
                'status' => 0,
                'data' => null,
                'error' => $response->get_error_message(),
            ];
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        $raw = wp_remote_retrieve_body($response);
        $decoded = null;
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
        }

        $ok = $status >= 200 && $status < 300;

        return [
            'ok' => $ok,
            'status' => $status,
            'data' => is_array($decoded) ? $decoded : null,
            'error' => $ok ? null : (is_array($decoded) && isset($decoded['errors'][0]['detail']) ? (string) $decoded['errors'][0]['detail'] : 'keygen_request_failed'),
        ];
    }
}
