<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'PropertyRequest' ) ) :

    final class PropertyRequest {
	static $url = "/api/property/";

	/**
	 * Uses the site url to get or create turnstile property
	 */
	public static function get_for_site() {
	    return new TurnstileAPIRequest(self::$url, "GET");
	}
    }

endif;
