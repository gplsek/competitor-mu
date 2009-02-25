<?php
/*
Plugin Name: Marquee
Plugin URI: 
Description: Hooks into Wordpresses media uploader and allows the user to use an image for a post in a flash file.
Version: 1.0
Author: George Plsek
Author URI: 
*/

### Load WP-Config File If This File Is Called Directly
if (!function_exists('add_action')) {
	require_once('../../../wp-config.php');
}

define('MARFOLDER', dirname(plugin_basename(__FILE__)));
include_once(ABSPATH . "wp-content/plugins/wp_marquee/wp_marquee_class.php");
include_once(ABSPATH . "wp-content/plugins/wp_marquee/wp_marquee_db.php");

//include_once(ABSPATH . "wp-content/plugins/wp_marquee/wp_magazine_size.php");
// include_once(ABSPATH . "/wp-content/plugins/wp_marquee/wp_marquee_xml.php");

// Installer and Uninstaller for our database
if(function_exists('register_activation_hook') && function_exists('register_deactivation_hook')) {	
	register_activation_hook(MARFOLDER . '/wp_marquee.php','marquee_db_install');
	register_deactivation_hook(MARFOLDER . '/wp_marquee.php','marquee_db_uninstall');	
}

// Hooks
// Marquee Admin Options to be set within the backend
add_action('admin_menu', 'set_manage_page');
function set_manage_page(){
	if (function_exists('add_submenu_page')) {
			add_management_page('Marquee', 'Marquee', 8, __FILE__, 'display_options');
	}
}

function display_options(){
	include_once(ABSPATH . "wp-content/plugins/wp_marquee/wp_marquee_options.php");
	marquee_options_page();
}

// Add our JavaScipt
function marquee_js(){
  echo '<script src="'.get_option('siteurl').'/wp-content/plugins/wp_marquee/js/wp_marquee.js" type="text/javascript"></script>';
  // wp_register_script( 'wp_marquee-js', get_option('siteurl') . '/wp-content/plugins/wp_marquee/js/wp_marquee.js', false, '' ); 
  // wp_enqueue_script('wp_marquee-js');
}

// add_action('wp_head', 'marquee_js');
// add_action('wp_print_scripts', 'marquee_js');

// wordpress hack for checking if the plugin exists
// if(function_exists('wp_marquee')) {}
function wp_marquee_plugin() {}

?>