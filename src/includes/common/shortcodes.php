<?php

defined( 'ABSPATH' ) or die( 'Unauthorized' );

if ( ! class_exists( 'TurnstileShortcodes' ) ) :

class TurnstileShortcodes {
    public function __construct() {
        $this->setup_globals();
        $this->add_shortcodes();
    }

    private function setup_globals() {
    }

    private function add_shortcodes() {
        add_shortcode( 'turnstile_link', array( $this, 'link_shortcode' ) );
        add_shortcode( 'turnstile_more', array( $this, 'readmore_shortcode' ) );
        add_shortcode( 'turnstile_settings', array( $this, 'settings_shortcode' ) );
    }

    // [turnstile link="http://turnstile.me/XXX"]
    // display a read more button redirecting to turnstile link
    // from custom field `turnstile-link` field or the `link` shortcode arg
    function link_shortcode( $atts ) {
        $a = shortcode_atts( array(
            'original'  => false,  // original url
            'link'      => false,  // turnstile link
            'type'      => 'more',  // turnstile link
            'text'      => __( 'Read More', 'turnstile' ),
            'class'     => '',  // css class
            'social'    => 'facebook,linkedin',  // enabled social platform
        ), $atts );
        if ( false !== $a['link'] ){
            $link = $a['link'];
        } else {
            $post_id = get_the_ID();
            $link = get_post_meta( $post_id, 'turnstile-link', true );
        }
        $cutoff = 'turnstile_more';
        if ( 'button' === $a['type']  ){
            $class = 'btn btn-turnstile';
        } elseif ( 'more' === $a['type'] ){
            $cutoff = 'turnstile_cutoff';
            $class = 'btn-turnstile-readmore';
        } else {
            $class = '';
        }
        if ( 'turnstile' === get_query_var( 'ref', false ) ){
            return;
        }

        ob_start(); ?>
            <?php if ( 'more' === $a['type'] ) : ?>
                <div class="<?php esc_attr_e( $cutoff ); ?>"></div>
            <?php endif; ?>
            <a href="<?php echo esc_url( $link ); ?>" title="<?php esc_attr_e( $a['text'] ); ?>" class="<?php esc_attr_e( $class . ' ' . $a['class'] ); ?>">
                <?php esc_html_e( $a['text'] ); ?>
            </a>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders a link allowing site users ability to manage their Turnstile data
     */
    function settings_shortcode( $atts ) {
        $a = shortcode_atts( array(
            'type'      => 'download',  // turnstile link
            'text'      => __( 'Turnstile data settings', 'turnstile' ),
            'class'     => '',  // css class
        ), $atts );

        $link = turnstile()->url('/settings/data');

        ob_start(); ?>
            <a href="<?php echo esc_url( $link ); ?>" title="<?php esc_attr_e( $a['text'] ); ?>" class="<?php esc_attr_e( $a['class'] ); ?>">
                <?php esc_html_e( $a['text'] ); ?>
            </a>
        <?php
        return ob_get_clean();
    }

    // Shortcode turnstile read more
    // [turnstile_more] content to hide [/turnstile_more]
    function readmore_shortcode( $atts, $content = null ) {
        if ( function_exists('cn_cookies_accepted') && ! cn_cookies_accepted() ) {
            return;
        }

        $a = shortcode_atts( array(
            'class' => '',
            'link'  => false,  // turnstile link
            'text'  => __( 'Read More', 'turnstile' ),
        ), $atts );
        $show_content = false;
        if ( turnstile()->ref === get_query_var( 'ref', false ) ){
            $show_content = true;
        }

        if ( false !== $a['link'] ){
            // get the link form the shortcode link argument
            $link = $a['link'];
        } elseif ( false === $a['link'] ) {
            // get the link from the post custom meta field
            $post_id = get_the_ID();
            $link = get_post_meta( $post_id, "turnstile-readmore-self", true );  // link to self article

            if ( '' === $link ){
                //
                $post_id = get_the_ID();
                $link = get_permalink( $post_id );
                $link = $link . "?ref=" . turnstile()->ref;
            }
        }

        if ( false === $show_content ){
            // if the user have not clicked yet on the turnstile link
            // we display the turnstile link
            ob_start();
            ?>
                <style>
                    @media all and (min-width: 750px) {
                        #TB_title { display: none; }
                        #TB_window { border-radius: 10px; }
                        #TB_ajaxContent { overflow: hidden !important;}
                    }
                    @media all and (max-width: 750px) {
                        #TB_title { display: none; }
                        #TB_window {
                            border-radius: 10px !important;
                            width: 320px !important;
                            margin-left: -160px !important;
                        }
                        #TB_ajaxContent {
                            padding: 2px 20px 15px 10px !important;
                            width: 330px !important;
                            overflow: hidden !important;
                        }
                    }
                </style>
                <script>
                    turnstile.doLogin("<?php echo esc_url( $link ); ?>");
                </script>

                <div class="turnstile_cutoff"></div>

                <?php add_thickbox(); ?>
                <a href="#TB_inline?width=500&height=500&inlineId=ts-login" class="thickbox btn-turnstile-readmore <?php esc_attr_e( $a['class'] ); ?>">
                    <button class="btn-turnstile-readmore <?php esc_attr_e( $a['class'] ); ?>">
                        <?php esc_html_e( $a['text'] ); ?>
                    </button>
                </a>
            <?php
            return ob_get_clean();
        } elseif ( $show_content ) {
            // user clicked on turnstile link, so we show content
            // we also are hiding the url param ?ref=turnstile to avoid users sharing the full-access link
            ob_start();
            ?>
            <span id='tsreadmore'></span>
            <script>
/*
                document.onreadystatechange = function () {
                    if ( document.readyState == 'interactive' ) {  // vanillajs $.ready()
                        // remove ref
                        var newlocation = document.location.toString();
                        newlocation = newlocation.replace("?ref=turnstile.me", "");
                        newlocation = newlocation.replace("&ref=turnstile.me", "");
                        window.history.pushState(false, false, newlocation);   // hide ref
                        document.getElementById('tsreadmore').scrollIntoView();  // scroll to readmore
                    }
                }
*/
            </script>
            <?php
            echo wp_kses_post( wpautop( $content ) );
            return ob_get_clean();
        }
    }
}
endif;
