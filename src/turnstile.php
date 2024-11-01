<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Turnstile' ) ) :

final class Turnstile {
    public static function instance() {

            // Store the instance locally to avoid private static replication
            static $instance = null;

            // Only run these methods if they haven't been ran previously
            if ( null === $instance ) {
                    $instance = new Turnstile;
                    $instance->setup_environment();
                    $instance->includes();
                    $instance->setup_variables();
                    $instance->setup_actions();
                    $instance->setup_filters();
            }

            // Always return the instance
            return $instance;
    }

    private function __construct() { /* Do nothing here */ }

    public function setup_environment() {
        $this->file         = __FILE__;
        $this->basename     = apply_filters( 'turnstile_plugin_basename', str_replace( array( 'build/', 'src/' ), '', plugin_basename( $this->file ) ) );
        $this->basepath     = apply_filters( 'turnstile_plugin_basepath', trailingslashit( dirname( $this->basename ) ) );

        $this->plugin_url   = apply_filters( 'turnstile_plugin_dir_url',  plugin_dir_url ( $this->file ) );
    }

    public function includes() {
        if ( file_exists( plugin_dir_path(__FILE__) . 'config/overrides.php' ) ) {
            include plugin_dir_path(__FILE__) . 'config/overrides.php';
        }

        require plugin_dir_path(__FILE__) . 'config/base.php';

        require plugin_dir_path(__FILE__) . 'includes/core/abstraction.php';
        require plugin_dir_path(__FILE__) . 'includes/core/functions.php';
        require plugin_dir_path(__FILE__) . 'includes/core/options.php';

        require plugin_dir_path(__FILE__) . 'includes/core/update.php';

        require plugin_dir_path(__FILE__) . 'includes/common/shortcodes.php';

        require plugin_dir_path(__FILE__) . 'includes/core/actions.php';

        require plugin_dir_path(__FILE__) . 'includes/core/api/class-turnstile-api.php';
        require plugin_dir_path(__FILE__) . "includes/core/api/class-turnstile-request.php";
        require plugin_dir_path(__FILE__) . "includes/core/api/class-turnstile-link.php";
        require plugin_dir_path(__FILE__) . "includes/core/api/class-turnstile-token.php";
        require plugin_dir_path(__FILE__) . "includes/core/api/class-turnstile-client-token.php";
        require plugin_dir_path(__FILE__) . "includes/core/api/class-turnstile-user.php";
        require plugin_dir_path(__FILE__) . "includes/core/api/class-turnstile-property.php";

        if (is_admin()) {
            require plugin_dir_path(__FILE__) . 'includes/admin/actions.php';
        }
    }

    public function setup_variables() {
        $this->scopes = array(
            'turnstile',
            // Add more scopes as needed
        );

        $this->ref = 'turnstile.me';

        $this->url_base = TURNSTILE_URL_BASE;
        $this->api_base = TURNSTILE_API_BASE;

        $this->scope_string = implode('%20', $this->scopes);
        $this->redirect_uri = admin_url() . 'admin-ajax.php?action=add_turnstile_code';

        // Display installation screen
        $this->debug_installation_screen = false;
    }

    public function setup_actions() {
        // Add actions to plugin activation and deactivation hooks
        add_action( 'activate_'   . $this->basename, 'turnstile_activation'   );
        add_action( 'deactivate_' . $this->basename, 'turnstile_deactivation' );

        add_action( 'turnstile_deactivation',        array( $this, 'turnstile_deactivation' ) );
        add_action( 'turnstile_register_shortcodes', array( $this, 'register_shortcodes' ) );
        add_action( 'turnstile_head',                array( $this, 'turnstile_head' ) );
        add_action( 'turnstile_footer',              array( $this, 'turnstile_footer' ) );
        add_action( 'wp_enqueue_scripts',            array( $this, 'theme_enqueue_styles' ) );
    }

    private function setup_filters() {
        add_filter( 'allowed_redirect_hosts' , array( $this, 'allowed_redirect_hosts' ) , 10 );
    }

    private function allowed_redirect_hosts($content){
      $content[] = 'turnstile.me';
      return $content;
    }

    public function turnstile_deactivation() {
        $this->remove_options();
    }

    public function register_shortcodes() {
        $this->shortcodes = new TurnstileShortcodes();
    }

    public function turnstile_head() { }

    public function turnstile_footer() {
        wp_enqueue_script('turnstile_client_js', 'https://turnstile.me/static/js/client/turnstile.umd.js');
        wp_add_inline_script('turnstile_client_js',
            '(new turnstile.Turnstile({propId: "'
            . esc_html( get_option("turnstile_property")["id"] )
            . '", auth: {type: "apikey", key: "'
            . esc_html( get_option("turnstile_client_token") )
            .'"}})).init().then(() => {});');
    }

    public function url() {
        return rtrim($this->url_base, '/') .'/'. ltrim(implode(func_get_args(),'/'), '/');
    }

    public function api_url() {
        return rtrim($this->api_base, '/') .'/'. ltrim(implode(func_get_args(),'/'), '/');
    }

    public function property_id() {
        $property = get_option('turnstile_property');
        if ( !empty($property) ) {
            return $property['id'];
        } else {
            return '';
        }
    }

    public function is_connected() {
        $client = get_option('turnstile_api');
        return !empty($client);
    }

    public function settings_url() {
        return admin_url() . 'options-general.php?page=turnstile-setting-admin';
    }

    public function generate_app_name() {
        return str_replace('.', '_', wp_parse_url(get_site_url(), PHP_URL_HOST));
    }

    public function js_path($js){
        return $this->plugin_url . 'public/js/' . $js;
    }

    public function img_path($img){
        return $this->plugin_url . 'public/img/' . $img;
    }

    public function css_path($css){
        return $this->plugin_url . 'public/css/' . $css;
    }

    public function vendor_path($path){
        return $this->plugin_url . 'public/vendor/' . $path;
    }

    public function connect_url() {
        $nonce = wp_create_nonce( 'connect-to-turnstile' );
        return $this->url('/o/applications/register/')
          . '?name=' . $this->generate_app_name()
          . '&redirect_uri=' . rawurlencode($this->redirect_uri)
          . '&state=' . $nonce;
    }

    public function api() {
        return client($this->url_base);
    }

    // Add turnstile.js
    function theme_enqueue_styles() {
        if ( function_exists('cn_cookies_accepted') && !cn_cookies_accepted() ) {
            return;
        }
        wp_enqueue_script( 'turnstile_js', $this->js_path('turnstile.js'), array('jquery') );
        wp_enqueue_style( 'turnstile_css', $this->css_path('style.css'), false );
    }

    // add the ref variable to wp accessible variable
    // used in ?ref=turnstile for turnstile_more shortcode
    function add_query_vars_filter( $vars ){
        $vars[] = "ref";
        return $vars;
    }

    function remove_options() {
        $options = [ 'api', 'code', 'access_token', 'refresh_token', 'token_type', 'scope', 'access_token_expires', 'client_token', 'user', 'property' ];
        foreach ( $options as $option ) {
            delete_option( 'turnstile_' . $option );
        }
    }

    function social_array(){
        $social_available = array(
            'facebook'  => 'fb',
            'linkedin'  => 'lk',
            'twitter'   => 'tw',
            'google'    => 'google',
            'pinterest' => 'pin',
            'instagram' => 'insta',
            'github'    => 'gh',
            'snapchat'  => 'snap');

        $social = array();
        foreach ($social_available as $key => $value) {
            $social['ask_' . $value] = False;
            if ( $this->ask_get_meta( $key ) === "1" ) {
                $social['ask_' . $value] = True;
            }
        }
        return $social;
    }

    /**
     * Gets whether or not social login for $platform is enabled on current
     * post
     */
    function ask_get_meta( $platform ) {
	global $post;
	$array = get_post_meta( $post->ID, 'turnstile_ask', true);
	if ( $array ) {
	    $array = stripslashes_deep( $array );
	    return wp_kses_decode_entities( $array[$platform] );
	} else {
	    return "empty";
	}
    }

    // Add a turnstile link to the post custom field when it's published or saved
    // this adds the turnstile-readmore-self field to the article, enabling
    // the shortcode [turnstile_more] content to hide [/turnstile_more]
    // without the need of any arg like [turnstile_more link="http://external-link.com"]
    // external links can be used to send user at the original source article
    function publish_event_add_turnstile_link_more( $ID ) {
        $permalink = get_permalink( $ID );
        $turnstile_link = get_post_meta( $ID, 'turnstile-readmore-self' );

        if ( false === $turnstile_link || 0 === strlen( $turnstile_link[0] ) ) {
            $request = LinkRequest::create( $permalink, true, $this->social_array() );
        } else {
            $parts = explode( '/', $turnstile_link[0] ) ;
            $turnstile_name = end($parts);
            $request = LinkRequest::update( $turnstile_name, true, $this->social_array() );
        }

        $body = $this->api()->do_request( $request );
        if ( is_wp_error( $body ) ) {
            return $body;
        }

        if ( isset( $body['link'] ) ) {
            $turnstile_link = $this->url($body['link']);
            update_post_meta( $ID, 'turnstile-readmore-self', $turnstile_link );
        } elseif ( isset( $body['name'] ) ) {
            $turnstile_link = str_replace( "b'", "", $body['name']);
            $turnstile_link = str_replace( "'", "", $turnstile_link);
            $turnstile_link = $this->url( $turnstile_link );
            update_post_meta( $ID, 'turnstile-readmore-self', $turnstile_link );
        } else {
            // error
        }
        return True;
    }
}

function turnstile() {
    return Turnstile::instance();
}

turnstile();

endif;
