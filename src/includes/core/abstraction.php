<?php

defined( 'ABSPATH' ) || exit;

function turnstile_setup_admin() {
	$turnstile = turnstile();

	// Skip if already setup
	if ( empty( $turnstile->admin ) ) {

		// Require the admin class
		require_once plugin_dir_path(__FILE__) . '../../includes/admin/class-turnstile-admin.php';

		// Setup
		$turnstile->admin = class_exists( 'TurnstileAdmin' )
			? new TurnstileAdmin()
			: new stdClass();
	}

	// Return the admin object
	return $turnstile->admin;

}
