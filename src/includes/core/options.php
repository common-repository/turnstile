<?php

defined( 'ABSPATH' ) || exit;

function turnstile_get_default_options() {
	return (array) apply_filters( 'turnstile_get_default_options', array(
		'turnstile_access_token'       => '',
		'turnstile_api'                => array(),
		'turnstile_code'               => '',
		'turnstile_refresh_token'      => '',
		'turnstile_scope'              => '',
		'turnstile_token_type'         => '',
	));
}

function turnstile_add_options() {
    // Add default options
    foreach ( turnstile_get_default_options() as $key => $value ) {
		add_option( $key, $value );
    }

    // Allow previously activated plugins to append their own options.
    do_action( 'turnstile_add_options' );
}

function turnstile_delete_options() {
   // Add default options
   foreach ( array_keys( turnstile_get_default_options() ) as $key ) {
	   delete_option( $key );
   }

   // Allow previously activated plugins to append their own options.
   do_action( 'turnstile_delete_options' );
}
