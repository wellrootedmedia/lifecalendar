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

add_action( 'wp_enqueue_scripts', 'pluginScripts' );
function pluginScripts() {
    wp_enqueue_style( 'jquery-ui-smoothness', "//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css" );
    wp_enqueue_style( 'custom-plugin-style', plugins_url( 'custom.css' , __FILE__ ) );
}

add_shortcode( 'life_calendar', 'life_calendar_func' );
function life_calendar_func( $atts ) {
    displayCalendarClass();
}
