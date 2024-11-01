<?php
/**
Plugin Name: Turnstile
Plugin URI: https://turnstile.me
Description: Turnstile Analytics
Version: 1.4.0
Author: bespokeapp
Author URI: https://bespoke.app
License: GPLv2
 */

defined( 'ABSPATH') or die( 'Unauthorized' );

define( 'TURNSTILE_PLUGIN_VERSION', '1.4.0'  );

define( 'TURNSTILE_PLUGIN_PATH', plugin_dir_url( __FILE__ ));

// Assume you want to load from build
$turnstile_loader = __DIR__ . '/build/turnstile.php';

// Load from source if no build exists
if ( ! file_exists( $turnstile_loader ) || defined( 'TURNSTILE_LOAD_SOURCE' ) ) {
        $turnstile_loader = __DIR__ . '/src/turnstile.php';
}

include $turnstile_loader;

// Unset the loader, since it's loaded in global scope
unset( $turnstile_loader );
