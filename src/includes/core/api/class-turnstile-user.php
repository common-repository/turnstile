<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'UserRequest' ) ) :

    final class UserRequest {
	public static function me() {
	    return new TurnstileAPIRequest( "/api/me/", "GET" );
	}
    }

endif;
