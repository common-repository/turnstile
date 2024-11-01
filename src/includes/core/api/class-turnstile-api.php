<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TurnstileAPIClient' ) ) :

    final class TurnstileAPIClient {
	private $url_base;

	public static function instance($url_base) {

	    // Store the instance locally to avoid private static replication
	    static $instance = null;

	    // Only run these methods if they haven't been ran previously
	    if ( null === $instance ) {
		$instance = new TurnstileAPIClient;
		$instance->url_base = $url_base;
	    }

	    // Always return the instance
	    return $instance;
	}

	function includes() {
	}

	private function __construct() { /* Do nothing here */ }

	public function set_code( $code ) {
	    update_option( 'turnstile_authorization_code', $code );
	}

	function token( $force_refresh = False ) {
	    $client_id = get_option( 'turnstile_api' )['client_id'];
	    $client_secret = get_option( 'turnstile_api' )['client_secret'];
	    $code = get_option( 'turnstile_authorization_code' );

	    if ( !empty( $code ) ) {
		$request = TokenRequest::code( $client_id, $client_secret, $code );
		$response = $this->do_request( $request, false );
		if ( is_wp_error( $response ) ) {
		    return $response;
		}
		delete_option( 'turnstile_authorization_code' );
	    } else {
		$expiration = get_option( 'turnstile_access_token_expires' );
		$access_token = get_option( 'turnstile_access_token' );
		if ( !$force_refresh && !empty( $expiration ) && !empty( $access_token ) && time() < ($expiration - 60) ) {
		    return $access_token;
		}

		$refresh_token = get_option( 'turnstile_refresh_token' );
		$request = TokenRequest::refresh( $client_id, $client_secret, $refresh_token );
		$response = $this->do_request( $request, false );
		if ( is_wp_error( $response ) ) {
		    return $response;
		}
	    }

	    if (isset($response['access_token'])){
		update_option( 'turnstile_access_token', $response['access_token'] );
		update_option( 'turnstile_access_token_expires', time() + $response['expires_in'] );
		update_option( 'turnstile_refresh_token', $response['refresh_token'] );
		update_option( 'turnstile_token_type', $response['token_type'] );
		update_option( 'turnstile_scope', $response['scope'] );
		return $response['access_token'];
	    }
	    return False;
	}

	public function do_request( $request, $needs_auth = True ) {
	    $headers = array();
	    if ( $needs_auth ) {
		$token = $this->token();
		if ( is_wp_error( $token ) ) {
		    return $token;
		}
		$headers["Authorization"] = "Bearer " . $token;
	    }

	    $args = array(
		'sslverify' => TURNSTILE_ENV !== 'dev',
		'method' => $request->method,
		'timeout' => 3,
		'redirection' => 2,
		'headers' => array_merge($headers, $request->headers),
	    );
	    if ( $request->method !== 'GET' ) {
		$args['body'] = $request->body();
	    }

	    $response = wp_remote_post( $this->url_base . $request->url, $args );
	    if ( is_wp_error( $response ) ) {
		return $response;
	    }

	    if ( is_array( $response ) ) {
		$body = json_decode( $response['body'], True );
	    }

	    return $body;
	}
    }

function client($url_base) {
    return TurnstileAPIClient::instance($url_base);
}

endif;
