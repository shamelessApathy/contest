
<?php
   /*
   Plugin Name: Daily Contest
   Plugin URI: none
   Description: a plugin to do daily contest entry
   Version: 1.0
   Author: Brian Moniz
   Author URI: http://slcutahdesign.com
   License: GPL2
   */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action( 'wp_enqueue_scripts', 'register_my_script' );

function register_my_script() 
{
wp_register_script( 'daily-contest', plugins_url( '/js/daily_contest.js' , __FILE__ ), array(), '1.0.0', true );
}

$func = function() 
{
	require_once('shortcode/front.contest_main.php');
	wp_enqueue_script( 'daily-contest' );
};
add_shortcode( 'display-contest',$func);




// Porperly enqueue files for admin area menu
function load_custom_wp_admin_style($hook) {
        // Load only on ?page=mypluginname
        if($hook != 'toplevel_page_mypluginname') {
                return;
        }
        wp_enqueue_style( 'dc-admin-styles', plugins_url('css/dc-admin-styles.css', __FILE__) );
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );












// End admin area menu
// Add the admin panel page here
function admin_page()
{
	$entries = getEntries();
	$path = ABSPATH . 'wp-content/plugins/daily_contest/css/dc-admin-styles.css';

	require_once('shortcode/admin.main.php');
}
add_action('admin_menu', 'daily_contest_menu');
 
function daily_contest_menu(){
        add_menu_page( 'Daily Contest Plugin Page', 'Daily-Contest Plugin', 'manage_options', 'daily-contest', 'admin_page' );
}

// Function  to get all DB entries in daily_contest

function getEntries()
{
	$entries = require_once('get_entries.php');
	return $entries;
	
}

function createTables()
{
	global $wpdb;
  	global $your_db_name;
 
	// create the ECPT metabox database table
	if($wpdb->get_var("show tables like '$your_db_name'") != $your_db_name) 
	{
		$sql = "CREATE TABLE daily_contest (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`email` tinytext NOT NULL,
		`first_name` tinytext NOT NULL,
		`last_name` tinytext NOT NULL,
		`street_address` tinytext NOT NULL,
		`city` tinytext NOT NULL,
		`state` tinytext NOT NULL,
		`zipcode` tinytext NOT NULL,
		`created_at` int NOT NULL
		UNIQUE KEY id (id)
		);";
 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

	// run install scripts when plugin is activated
	register_activation_hook(__FILE__, 'createTables');

?>