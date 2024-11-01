<?php

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'turnstile_init', 0 );

add_action( 'turnstile_init', 'turnstile_register', 10);

add_action( 'turnstile_register', 'turnstile_register_shortcodes', 10 );

add_action( 'turnstile_activation', 'turnstile_add_activation_redirect' );

add_action( 'wp_head', 'turnstile_head' );
add_action( 'wp_footer', 'turnstile_footer' );

function turnstile_activation() {
	do_action( 'turnstile_activation' );
}

function turnstile_deactivation() {
	do_action( 'turnstile_deactivation' );
}

function turnstile_init() {
	do_action( 'turnstile_init' );
}

function turnstile_register() {
	do_action( 'turnstile_register' );
}

function turnstile_register_shortcodes() {
	do_action( 'turnstile_register_shortcodes' );
}

function turnstile_head() {
	do_action( 'turnstile_head' );
}

function turnstile_footer() {
	do_action( 'turnstile_footer' );
}
