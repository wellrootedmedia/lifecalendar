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
include( plugin_dir_path( __FILE__ ) . 'connect-class.php');
include( plugin_dir_path( __FILE__ ) . 'calendar-class.php');
include( plugin_dir_path( __FILE__ ) . 'timeline-class.php');

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
    wp_enqueue_script( 'my-jquury-script', "http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" );
    wp_enqueue_script('my-jquery-ui-script', 'http://code.jquery.com/ui/1.11.1/jquery-ui.js');

    wp_enqueue_style( 'timeline-style', plugins_url( 'css/style.css' , __FILE__ ) );
    wp_enqueue_script('timeline-script', plugins_url( 'js/script.js' , __FILE__ ));
    wp_enqueue_script('timeline-ui-script', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js');
}

add_shortcode( 'life_calendar', 'life_calendar_func' );
function life_calendar_func( $atts ) {
//    extract(shortcode_atts(array(
//        'defaultMonth' => "",
//        'defaultYear' => ""
//    ), $atts));

    displayCalendarClass();
    $lceOption = get_option('timeline_option');
    if($lceOption == "on")
        displayTimelineClass();
}





add_action('admin_menu', 'gmp_create_menu');
function gmp_create_menu() {
    add_submenu_page( 'edit.php?post_type=life_calendar_events', 'settings', 'Settings', 'manage_options', 'life-calendar-settings', 'life_calendar_settings' );
    add_action('admin_init', 'lce_register_settings');
}

function lce_register_settings() {
    register_setting('lce-settings-group','timeline_option');
}

function life_calendar_settings() {
    ?>
    <div class="wrap">
        <h2><?php _e('Life Calendar Settings', 'lce-plugin'); ?></h2>

        <form method="post" action="options.php">
            <?php settings_fields('lce-settings-group');
            $lceOption = get_option('timeline_option');
            $checked = ($lceOption == "on" ? "checked=\"checked\"" : "");?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Enable time line'); ?></th>
                    <td><input type="checkbox" name="timeline_option" <?php echo $checked; ?>/></td>
                </tr>
            </table>
            <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save'); ?>"/></p>
        </form>
    </div>
    <?php
}