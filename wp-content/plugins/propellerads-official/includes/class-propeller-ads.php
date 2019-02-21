<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Propeller_Ads
{

	/**
	 * @var      Propeller_Ads_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct()
	{
		$this->plugin_name = 'propeller-ads';
		$this->version = '2.0.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Propeller_Ads_Loader. Orchestrates the hooks of the plugin.
	 * - Propeller_Ads_i18n. Defines internationalization functionality.
	 * - Propeller_Ads_Admin. Defines all hooks for the admin area.
	 * - Propeller_Ads_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 */
	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-propeller-ads-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-propeller-ads-i18n.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-propeller-ads-settings-helper.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-propeller-ads-anti-adblock.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-propeller-ads-anti-adblock-client.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-propeller-ads-zone-helper.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-propeller-ads-messages.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-propeller-ads-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-propeller-ads-public.php';

		$this->loader = new Propeller_Ads_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale()
	{
		$plugin_i18n = new Propeller_Ads_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Propeller_Ads_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_init', $plugin_admin, 'register_session');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_settings_page');
		$this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
		$this->loader->add_action('admin_init', $plugin_admin, 'redirect_after_update');
		$this->loader->add_action('admin_init', $plugin_admin, 'auto_save_publisher_token');
		$this->loader->add_action('admin_notices', $plugin_admin, 'error_notices');
		$this->loader->add_action('update_option_propeller_ads_general_token', $plugin_admin, 'action_save_publisher_token');
		$this->loader->add_action('in_plugin_update_message-propeller-ads/propeller-ads.php', $plugin_admin,'action_in_plugin_update');
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Propeller_Ads_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_filter('wp_footer', $plugin_public, 'insert_script');
		$this->loader->add_action('wp_head', $plugin_public, 'insert_manifest');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Propeller_Ads_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}
}
