<?php
defined( 'ABSPATH' ) or die( 'Unauthorized' );

if ( ! class_exists( 'TurnstileAdmin' ) ) :

class TurnstileAdmin {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Holds metabox class instance
     */
    private $metaboxes;

    /**
     * Start up
     */
    public function __construct(){
        $this->setup_globals();
        $this->includes();
        $this->setup_variables();
        $this->setup_actions();
        // $this->setup_filters();
    }

    private function setup_globals() {
    }

    private function includes() {
        require plugin_dir_path(__FILE__) . 'class-turnstile-meta-boxes.php';
        require plugin_dir_path(__FILE__) . 'auth.php';
        require plugin_dir_path(__FILE__) . 'common.php';
        require plugin_dir_path(__FILE__) . 'tinymce/wysiwyg.php';
    }

    private function setup_variables() {
        $this->metaboxes = new TurnstileMetaBoxes();
    }

    private function setup_actions() {
        add_action( 'turnstile_admin_menu',  array( $this, 'admin_menus' ) );
        add_action( 'turnstile_admin_init',  array( $this, 'page_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

        add_action( 'turnstile_insert_post', array( $this, 'save_post'), 15);
    }

    public function enqueue_scripts_and_styles(){
        wp_enqueue_style( 'turnstile_admin_css', turnstile()->css_path('style_admin.css'), false );
        wp_enqueue_script( 'turnstile_admin_js', turnstile()->js_path('turnstile-admin.js'), array('jquery') );
    }

    /**
     * Add options page
     */
    public function admin_menus(){
        // This page will be under "Settings"
        add_options_page(
            'Turnstile',
            'Turnstile user tracking',
            'manage_options',
            'turnstile-setting-admin',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function render_admin_page(){
        // Set class property
        $this->options = get_option( 'turnstile_api' );
        if ( empty($this->options) || turnstile()->debug_installation_screen ){
            $this->render_admin_page_not_register();
        } else {
            $this->render_admin_page_already_register();
        }
    }

    public function render_admin_page_already_register(){
        ?>
            <div class="turnstile-settings-admin nowrap ts-center ts-gradient1 ts-H120">
                    <a href="https://turnstile.me/my_turnstiles" class="ts-clean-link">
                        <h1 class="ts-txt-white ts-margin0 ts-paddingV50 ts-txt-bigtitle">All done!</h1>
                    </a>
            </div>
            <div class="turnstile-settings-admin nowrap ts-center ts-paddingV50">
                <h2>Turnstile was <span class="ts-gradient1 ts-padding5">successfully</span> installed</h2>
            </div>
            <div style="display:flex">
              <a style="margin: 0 auto;font-size: 1.5rem;" target="_blank" href="https://turnstile.me/properties/<?php echo esc_html( turnstile()->property_id() ); ?>/">Configure your property</a>
            </div>
            <div class="turnstile-settings-admin nowrap ts-center ts-paddingV50">
                <div class="ts-maxwidth480 ts-margin0 ts-alignleft">
                    <p class="ts-center ts-txt-bigsubtitle">Now what?</p>
                    <h3><span class="ts-gradient1 ts-padding5 ts-bold">1</span>  Discover your audience</h3>
                    <ol>
                        <li>Click the "new Turnstile" button in your visual editor
                        <img height="24" width="24" class="ts-icon_turnstile" src="<?php echo esc_url( turnstile()->img_path("turnstile_logo_64.png") ); ?>"></li>
                        <li>
                            Put the content you want to track behind the shortcode
                        </li>
                        <li>
                            Select your preferred social-login providers from the Turnstile option box before publishing
                        </li>
                    </ol>

                    <h3><span class="ts-gradient1 ts-padding5 ts-bold">2</span>  Reach out to your audience</h3>
                    <ol>
                        <li>Contact information for each visitor will appear in your editor window</li>
                        <li>See all your metrics at <a href="https://turnstile.me/my_turnstiles">turnstile.me</a> </li>
                    </ol>
                    <h3><span class="ts-gradient1 ts-padding5 ts-bold">3</span>  Be creative!</h3>
                    <ol>
                        <li>Visit <a href="https://turnstile.me/">turnstile.me</a> to create smartlinks that track your followers on social media</li>
                        <li>Monitor bonus content and identify your most loyal fans</li>
                        <li>Organize a contest and pick influential winners</li>
                        <li>Visit us on Twitter <a href="https://twitter.com/TurnstileMe">@TurnstileMe</a> for more tips and updates</li>
                    </ol>

                </div>
            </div>
        <?php
    }

    public function render_admin_page_not_register(){
        ?>

            <div class="turnstile-settings-admin nowrap ts-center ts-gradient1 ts-H120">
                    <h1 class="ts-txt-white ts-margin0 ts-paddingV50 ts-txt-bigtitle">Great!</h1>
            </div>

            <div class="turnstile-settings-admin nowrap ts-center ts-paddingV50">
                <div class="ts-maxwidth480 ts-margin0 ts-alignleft">
                    <p class="ts-center">You successfully installed the Turnstile plugin.</p>
                    <p class="ts-center"> <strong> One last thing</strong>:  you need to connect to your Turnstile account.</p>
                </div>
            </div>

            <div class="turnstile-settings-admin wrap">

                <form method="post" action="admin-ajax.php?action=connect_with_turnstile">

                <?php
                    // This prints out all hidden setting fields
                    //settings_fields( 'turnstile_option_group' );
                    // do_settings_sections( 'turnstile-setting-admin' );
                    submit_button(__('Connect with Turnstile'), "button-primary draw meet");

                ?>
                </form>
            </div>
        <?php
    }


    /**
     * Register and add settings
     */
    public function page_init(){
        register_setting(
            'turnstile_option_group', // Option group
            'turnstile_api', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'plugin_settings',
            'Plugin Settings',
            array( $this, 'render_setting_section_description' ),
            'general'
            //'turnstile-setting-admin'
        );
    }

    function render_setting_section_description() {
        echo '<p>Turnstile Plugin Settings</p>';
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    function sanitize( $input ){
        $new_input = array();

        if( isset( $input['client_id'] ) )
            $new_input['client_id'] = sanitize_text_field( $input['client_id'] );
        if( isset( $input['client_secret'] ) )
            $new_input['client_secret'] = sanitize_text_field( $input['client_secret'] );
        if( isset( $input['timestamp'] ) )
            $new_input['timestamp'] = $input['timestamp'];
        return $new_input;
    }

    public function save_post( $post_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; };
        if ( ! isset( $_POST['turnstile_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['turnstile_nonce'] ), '_turnstile_nonce' ) ) { return; };
        if ( ! current_user_can( 'edit_post', $post_id ) ) { return; };

        $post_status = get_post_status( $post_id );
        $invalid_status = array( "draft", "auto-draft", "inherit", "trash" );
        if ( in_array( $post_status, $invalid_status, true ) ) { return; }

        $turnstile_ask = array();
        $turnstile_ask['facebook'] = ( isset( $_POST['turnstile_facebook'] ) ? 1 : 0);
        $turnstile_ask['linkedin'] = ( isset( $_POST['turnstile_linkedin'] ) ? 1 : 0);
        $turnstile_ask['twitter'] = ( isset( $_POST['turnstile_twitter'] ) ? 1 : 0);
        $turnstile_ask['google'] = ( isset( $_POST['turnstile_google'] ) ? 1 : 0);
        $turnstile_ask['instagram'] = ( isset( $_POST['turnstile_instagram'] ) ? 1 : 0);
        $turnstile_ask['github'] = ( isset( $_POST['turnstile_github'] ) ? 1 : 0);
        $turnstile_ask['pinterest'] = ( isset( $_POST['turnstile_pinterest'] ) ? 1 : 0);
        $turnstile_ask['snapchat'] = ( isset( $_POST['turnstile_snapchat'] ) ? 1 : 0);

        update_post_meta( $post_id, 'turnstile_ask', $turnstile_ask );

        return turnstile()->publish_event_add_turnstile_link_more( $post_id );
    }

}

endif;
