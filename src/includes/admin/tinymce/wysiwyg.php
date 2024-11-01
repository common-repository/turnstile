<?php
defined( 'ABSPATH' ) or die( 'Unauthorized' );

add_action('admin_head', 'add_turnstile_button');
function add_turnstile_button() {
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
   	return;
    }
	// check if WYSIWYG is enabled
	if ( get_user_option('rich_editing') === 'true') {
		add_filter("mce_external_plugins", "turnstile_add_tinymce_plugin");
		add_filter('mce_buttons', 'turnstile_register_button');
	}
}
function turnstile_add_tinymce_plugin($plugin_array) {
   	$plugin_array['turnstile_button'] = plugins_url( '/turnstile_button_plugin.js', __FILE__ ); // button script path
   	return $plugin_array;
}
function turnstile_register_button($buttons) {
   array_push($buttons, "turnstile_button");
   return $buttons;
}



