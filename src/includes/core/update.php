<?php

defined( 'ABSPATH' ) || exit;

function turnstile_add_activation_redirect() {
	// Bail if activating from network
	if ( is_network_admin() ) {
		return;
	}

	// Add the transient to redirect
	set_transient( 'turnstile_activation_redirect', true, 30 );
}
