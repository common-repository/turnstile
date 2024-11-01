<?php
defined( 'ABSPATH' ) || exit;

add_action( 'admin_head', 'turnstile_admin_head' );
add_action( 'admin_menu', 'turnstile_admin_menu' );
add_action( 'admin_init', 'turnstile_admin_init' );

add_action( 'add_meta_boxes', 'turnstile_add_meta_boxes' );
add_action( 'do_meta_boxes', 'turnstile_do_meta_boxes' );

add_action( 'wp_insert_post', 'turnstile_insert_post');
add_action( 'admin_notices', 'turnstile_error_message' );


// Hook onto admin_init
add_action( 'turnstile_admin_init', 'turnstile_do_activation_redirect', 1 );

// Initialize admin area
add_action( 'turnstile_init', 'turnstile_setup_admin');

function turnstile_admin_head() {
    do_action('turnstile_admin_head');
}

function turnstile_admin_menu() {
    do_action('turnstile_admin_menu');
}

function turnstile_admin_init() {
    do_action('turnstile_admin_init');
}

function turnstile_add_meta_boxes() {
    do_action('turnstile_add_meta_boxes');
}

function turnstile_do_meta_boxes() {
    do_action('turnstile_do_meta_boxes');
}

function turnstile_insert_post($postarr, $error = false) {
    do_action('turnstile_insert_post', $postarr, $error);
}
function turnstile_error_message() {
    do_action('turnstile_error_message');
}
