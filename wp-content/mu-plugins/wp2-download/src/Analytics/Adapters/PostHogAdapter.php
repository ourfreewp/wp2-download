<?php
// wp-content/mu-plugins/wp2-download/src/Analytics/Adapters/PostHogAdapter.php
namespace WP2\Download\Analytics\Adapters;

defined( 'ABSPATH' ) || exit();

use WP2\Download\Analytics\ConnectionInterface;

/**
 * PostHog analytics adapter.
 *
 * Config via constructor or constants:
 * - api_key (string) or WP2_POSTHOG_KEY
 * - host (string) or WP2_POSTHOG_HOST (defaults to https://app.posthog.com)
 * - timeout (int) default: 8
 */
class PostHogAdapter implements ConnectionInterface {

	/** @var string */
	protected $api_key = '';

	/** @var string */
	protected $host = 'https://app.posthog.com';

	/** @var int */
	protected $timeout = 8;

	/**
	 * @param array $config Optional config: api_key, host, timeout.
	 */
	public function __construct( array $config = [] ) {
		if ( isset( $config['api_key'] ) ) {
			$this->api_key = (string) $config['api_key'];
		} elseif ( defined( 'WP2_POSTHOG_KEY' ) ) {
			$this->api_key = (string) WP2_POSTHOG_KEY;
		}

		if ( isset( $config['host'] ) ) {
			$this->host = (string) $config['host'];
		} elseif ( defined( 'WP2_POSTHOG_HOST' ) ) {
			$this->host = (string) WP2_POSTHOG_HOST;
		}

		if ( isset( $config['timeout'] ) ) {
			$this->timeout = (int) $config['timeout'];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function connect(): bool {
		return $this->api_key !== '' && $this->host !== '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function track_event( string $event_name, array $properties = [] ): void {
		if ( ! $this->connect() ) {
			return;
		}

		// Accept a provided distinct_id in $properties; otherwise fall back to site URL.
		$distinct_id = '';
		if ( isset( $properties['distinct_id'] ) && is_string( $properties['distinct_id'] ) ) {
			$distinct_id = $properties['distinct_id'];
			unset( $properties['distinct_id'] );
		} else {
			$distinct_id = (string) wp_parse_url( home_url(), PHP_URL_HOST );
		}

		$body = [ 
			'api_key' => $this->api_key,
			'event' => sanitize_text_field( $event_name ),
			'distinct_id' => $distinct_id,
			'properties' => $properties,
		];

		$args = [ 
			'method' => 'POST',
			'timeout' => $this->timeout,
			'headers' => [ 
				'Content-Type' => 'application/json',
			],
			'body' => wp_json_encode( $body ),
			'data_format' => 'body',
			'blocking' => false, // fire-and-forget
		];

		$url = rtrim( $this->host, '/' ) . '/capture/';
		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			\WP2\Download\Util\Logger::log( 'PostHogAdapter error: ' . $response->get_error_message() . ' event=' . $event_name, 'ERROR' );
			return;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			$body_excerpt = '';
			if ( is_array( $response ) && isset( $response['body'] ) ) {
				$body_excerpt = substr( (string) $response['body'], 0, 200 );
			}
			\WP2\Download\Util\Logger::log( 'PostHogAdapter non-2xx: code=' . $code . ' event=' . $event_name . ' body=' . $body_excerpt, 'ERROR' );
		}
	}
}