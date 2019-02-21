<?php
/**
 * @package Cryptocurrency All-in-One
 */
/*
Plugin Name: Cryptocurrency All-in-One
Plugin URI: https://zwaply.com/
Description: Provides multiple cryptocurrency features: accepting payments, displaying prices and exchange rates, cryptocurrency calculator, accepting donations.
Version: 3.0.13
Author: Zwaply
Author URI: https://zwaply.com/
Text Domain: cryprocurrency-prices
Domain Path: /languages/
License: GPL2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//define plugin url global
define('CP_URL', plugin_dir_url( __FILE__ ));

//include source files
require_once( dirname( __FILE__ ) . '/includes/currencyall.class.php' );
require_once( dirname( __FILE__ ) . '/includes/cryptodonation.class.php' );
require_once( dirname( __FILE__ ) . '/includes/ethereum.class.php' );
require_once( dirname( __FILE__ ) . '/includes/widget.class.php' );
require_once( dirname( __FILE__ ) . '/includes/currencytickerwidget.class.php' );
require_once( dirname( __FILE__ ) . '/includes/common.class.php' );
require_once( dirname( __FILE__ ) . '/includes/cryptocurrency-ajax.php' );
require_once( dirname( __FILE__ ) . '/includes/cryptocurrency-dashboard-affiliate.php' );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
  require_once( dirname( __FILE__ ) . '/includes/admin.class.php' );
	add_action( 'init', array( 'CPAdmin', 'init' ) );
}

//define suported shortcodes
add_shortcode( 'allcurrencies', array( 'CPCurrencyAll', 'cp_all_currencies_shortcode' ) );
add_shortcode( 'cryptodonation', array( 'CPCryptoDonation', 'cp_cryptodonation_shortcode') );
add_shortcode( 'cryptoethereum', array( 'CPEthereum', 'cp_ethereum_shortcode' ) );
add_shortcode( 'currencyticker', array( 'CP_Ticker_Widget', 'shortcode' ) );

//this plugin requires js libraries
add_action( 'wp_enqueue_scripts', array( 'CPCommon', 'cp_load_scripts') );

//handle plugin activation
register_activation_hook( __FILE__, array( 'CPCommon', 'cp_plugin_activate') );

//add widget support
add_action('widgets_init', array( 'CPCommon', 'cp_widgets_init') );

//add custom stylesheet
add_action('wp_head', array( 'CPCommon', 'cp_custom_styles'), 100);
add_action( 'wp_enqueue_scripts', array( 'CPCommon', 'cp_enqueue_styles'), 15 );

//add admin stylesheet
add_action( 'admin_enqueue_scripts', array( 'CPCommon', 'cp_enqueue_admin_styles'), 15 );

//add translation
add_action('plugins_loaded', array( 'CPCommon', 'cp_load_textdomain'));