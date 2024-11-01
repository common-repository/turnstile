<?php
defined( 'ABSPATH' ) || exit;

function turnstile_do_activation_redirect() {
	// Bail if no activation redirect
	if ( ! get_transient( 'turnstile_activation_redirect' ) ) {
		return;
	}

	// Delete the redirect transient
	delete_transient( 'turnstile_activation_redirect' );

	// Bail if activating from network
	if ( is_network_admin() ) {
		return;
	}

	turnstile_redirect( add_query_arg( array( 'page' => 'turnstile-setting-admin' ), admin_url( 'options-general.php' ) ) );
}
