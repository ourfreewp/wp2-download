<?php
namespace WP2\Download\Development\Adapters;

defined( 'ABSPATH' ) || exit();

use WP2\Download\Development\ConnectionInterface;

/**
 * GitHub development adapter
 *
 * Validates connectivity to GitHub using a Personal Access Token and a simple
 * authenticated API request to `/user`.
 *
 * You can provide configuration via constructor or constants:
 * - token (string) or WP2_GITHUB_PAT
 * - api_base (string) default: https://api.github.com
 * - timeout (int) default: 12
 */
class GithubAdapter implements ConnectionInterface {
	/** @var string */
	protected $token;
	/** @var string */
	protected $api_base = 'https://api.github.com';
	/** @var int */
	protected $timeout = 12;

	/**
	 * @param array<string,mixed> $config
	 */
	public function __construct( array $config = [] ) {
		$this->token = (string) ( $config['token'] ?? ( defined( 'WP2_GITHUB_PAT' ) ? WP2_GITHUB_PAT : '' ) );
		$this->api_base = (string) ( $config['api_base'] ?? 'https://api.github.com' );
		if ( isset( $config['timeout'] ) ) {
			$this->timeout = (int) $config['timeout'];
		}
	}

	/** {@inheritdoc} */
	public function connect(): bool {
		if ( $this->token === '' ) {
			return false;
		}

		$url = rtrim( $this->api_base, '/' ) . '/user';
		$args = [ 
			'method' => 'GET',
			'timeout' => $this->timeout,
			'headers' => [ 
				'Authorization' => 'Bearer ' . $this->token,
				'Accept' => 'application/vnd.github+json',
				'User-Agent' => 'wp2-download (WordPress)'
			],
		];

		$response = wp_remote_request( $url, $args );
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		return $code >= 200 && $code < 300;
	}
}
