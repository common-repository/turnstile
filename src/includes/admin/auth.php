<?php
defined( 'ABSPATH' ) or die( 'Unauthorized' );

/**
 * Redirects to connection url with nonce
 */
function connect_with_turnstile() {
    wp_redirect(turnstile()->connect_url());
    exit();
}

add_action( 'wp_ajax_nopriv_connect_with_turnstile', 'connect_with_turnstile' );
add_action( 'wp_ajax_connect_with_turnstile', 'connect_with_turnstile' );

function oauth_complete_redirect( $status ) {
    $nonce = wp_create_nonce( 'turnstile-oauth-done' );
    header('Location: ' . admin_url('/options-general.php'
        . '?page=turnstile-setting-admin'
        . '&authorized=' . $status
        . '&_wpnonce=' . $nonce
    ));
    exit();
}

// Get code param and save it in options
function add_turnstile_code(){
    if ( empty( $_GET['state'] ) ) {
        die( "Request missing nonce" );
    }

    $nonce = sanitize_text_field( $_GET['state'] );
    if ( !wp_verify_nonce( $nonce, 'connect-to-turnstile' ) ) {
        die( "Invalid nonce" );
    }

    $code = isset($_GET['code']) ? sanitize_text_field( $_GET['code'] ) : null;
    $client_id = isset($_GET['client_id']) ? sanitize_text_field( $_GET['client_id'] ) : null;
    $client_secret = isset($_GET['client_secret']) ? sanitize_text_field( $_GET['client_secret'] ) : null;

    if ( !empty($client_id) && empty( $code ) ){
        $authorize_uri = turnstile()->url("/o/authorize/"
            . "?client_id={$client_id}"
            . "&client_secret={$client_secret}"
            . "&scope=". turnstile()->scope_string . "&response_type=code"
            . "&state=". wp_create_nonce( "connect-to-turnstile" ) );

        update_option( 'turnstile_api', [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'timestamp' => (new DateTime('NOW'))->format('c'),
            ]);

        wp_redirect($authorize_uri);
        exit();
    } else if ( empty($code) ){
        // why are you even here then?
        wp_safe_redirect(admin_url() . '/options-general.php?page=turnstile-setting-admin');
        exit();
    }

    turnstile()->api()->set_code($code);

    $user = turnstile()->api()->do_request(UserRequest::me());

    if ( is_wp_error( $user ) ) {
        return oauth_complete_redirect( 'error' );
    }

    update_option( 'turnstile_user', $user );

    $property = turnstile()->api()->do_request(PropertyRequest::get_for_site());

    if ( is_wp_error( $property ) ) {
        return oauth_complete_redirect( 'error' );
    }

    update_option( 'turnstile_property', $property );

    $token = turnstile()->api()->do_request(ClientTokenRequest::get_for_site());

    if ( is_wp_error( $token ) ) {
        return oauth_complete_redirect( 'error' );
    }

    update_option( 'turnstile_client_token', $token['key'] );

    // redirect to admin page
    return oauth_complete_redirect( 'success' );
}

add_action( 'wp_ajax_nopriv_add_turnstile_code', 'add_turnstile_code' );
add_action( 'wp_ajax_add_turnstile_code', 'add_turnstile_code' );
