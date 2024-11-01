<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ClientTokenRequest' ) ) :

    final class ClientTokenRequest {
	static $url = "/api/token/";

	/**
	 * Uses the site url to get or create user's client token
	 */
	public static function get_for_site() {
	    return new TurnstileAPIRequest(self::$url, "GET");
	}
    }

endif;
