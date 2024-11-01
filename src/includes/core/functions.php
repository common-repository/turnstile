<?php
defined( 'ABSPATH' ) || exit;

function turnstile_redirect( $location = '', $status = 302 ) {

	// Prevent errors from empty $location
	if ( empty( $location ) ) {
		$location = get_site_url();
	}

	// Setup the safe redirect
	wp_safe_redirect( $location, $status );

	// Exit so the redirect takes place immediately
	exit();
}

