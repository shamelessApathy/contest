<?php

/**
 * Plugin Name:       PropellerAds Official Plugin
 * Plugin URI:        https://wordpress.org/plugins/propellerads-official/
 * Description:       PropellerAds code integration
 * Version:           2.0.1
 * Author:            PropellerAds
 * Author URI:        https://propellerads.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       propeller-ads
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

function activate_propeller_ads()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-propeller-ads-activator.php';
	Propeller_Ads_Activator::activate();
}

function deactivate_propeller_ads()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-propeller-ads-deactivator.php';
	Propeller_Ads_Deactivator::deactivate();    // TODO: deregister options
}

register_activation_hook(__FILE__, 'activate_propeller_ads');
register_deactivation_hook(__FILE__, 'deactivate_propeller_ads');

require plugin_dir_path(__FILE__) . 'includes/class-propeller-ads.php';

function run_propeller_ads()
{
	$plugin = new Propeller_Ads();
	$plugin->run();
}

run_propeller_ads();
