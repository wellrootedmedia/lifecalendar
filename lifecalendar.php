<?php
/**
 * Plugin Name: Life Calendar
 * Plugin URI: http://shawnnolan.com/plugins/life-calendar
 * Description: A simple plugin for your life.
 * Version: 1.4
 * Author: Shawn Nolan
 * Author URI: http://shawnnolan.com
 * License: GPL2
 */

include( plugin_dir_path( __FILE__ ) . 'calendar-class.php');

add_action( 'init', 'create_post_type' );
function create_post_type() {
    register_post_type( 'life_calendar_events',
        array(
            'labels' => array(
                'name' => __( 'Events' ),
                'singular_name' => __( 'Event' )
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'current-event'),
            'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'revisions')
        )
    );
}

add_action('admin_menu', 'gmp_create_menu');
function gmp_create_menu() {
    add_submenu_page( 'edit.php?post_type=life_calendar_events', 'settings', 'Settings', 'manage_options', 'life-calendar-settings', 'life_calendar_settings' );
}
function life_calendar_settings() {
    echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
    echo '<h2>Life Calendar Settings Page</h2>';
    echo '</div>';
}

add_action( 'wp_enqueue_scripts', 'pluginScripts' );
function pluginScripts() {
    wp_enqueue_style( 'jquery-ui-smoothness', "//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" );
    wp_enqueue_style( 'custom-plugin-style', plugins_url( 'custom.css' , __FILE__ ) );
    wp_enqueue_script( 'my-jquury-script', "http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" );
    wp_enqueue_script('my-jquery-ui-script', 'http://code.jquery.com/ui/1.11.1/jquery-ui.js');
    /*
     *  <link href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel="stylesheet">
      <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
      <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
     */
}

add_shortcode( 'life_calendar', 'life_calendar_func' );
function life_calendar_func( $atts ) {
//    extract(shortcode_atts(array(
//        'defaultMonth' => "",
//        'defaultYear' => ""
//    ), $atts));

    displayCalendarClass();
}
