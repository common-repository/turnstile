<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'TokenRequest' ) ) :

    final class TokenRequest {

	static $url = "/o/token/";

	public static function refresh($client_id, $client_secret, $refresh_token) {
	    $data = array(
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'refresh_token',
		'refresh_token' => $refresh_token,
	    );
	    $headers = array(
		"Content-Type"  =>  "application/x-www-form-urlencoded",
	    );
	    return new TurnstileAPIRequest(self::$url, "POST", $data, $headers);
	}

	public static function code($client_id, $client_secret, $auth_code) {
	    $data = array(
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'authorization_code',
		'code' => $auth_code,
	    );
	    $headers = array(
		"Content-Type"  =>  "application/x-www-form-urlencoded",
	    );
	    return new TurnstileAPIRequest(self::$url, "POST", $data, $headers);
	}
    }

endif;
