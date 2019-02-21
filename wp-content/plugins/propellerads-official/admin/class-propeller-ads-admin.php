<?php

/**
 * The admin-specific functionality of the plugin.
 */
class Propeller_Ads_Admin
{
	/** @var string $ssp_route SSP url for getting Anti AdBlock token */
	private $ssp_route = 'https://publishers.propellerads.com';

	/**
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * @var Propeller_Ads_Settings_Helper $setting_helper Settings helper instance
	 */
	private $setting_helper;

	/**
	 * @var Propeller_Ads_Zone_Helper $zone_helper Settings helper instance
	 */
	private $zone_helper;

	/**
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->setting_helper = new Propeller_Ads_Settings_Helper($this->plugin_name);
		$this->zone_helper = new Propeller_Ads_Zone_Helper($this->plugin_name);
	}

	/**
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/propeller-ads-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/propeller-ads-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Add an settings page to the main menu
	 */
	public function add_settings_page()
	{
		// TODO: check https://developer.wordpress.org/reference/functions/add_menu_page/#notes about capabilities
		add_menu_page(
			__('PropellerAds', 'propeller-ads'),
			__('PropellerAds', 'propeller-ads'),
			'administrator',
			$this->plugin_name,
			array($this, 'display_options_page'),
			'none',
			76  // right after the 'Tools' submenu
		);
	}

	/**
	 * Render the options page
	 */
	public function display_options_page()
	{
		include_once 'partials/propeller-ads-admin-display.php';
	}

	/**
	 * Register all plugin settings
	 */
	public function register_settings()
	{
		$this->setting_helper->add_section(array(
			'id' => 'general',
			'title' => 'General',
		));

		$this->setting_helper->add_field(array(
			'section' => 'general',
			'id' => 'logged_in_disabled',
			'title' => 'Membership',
			'type' => Propeller_Ads_Settings_Helper::FIELD_TYPE_CHECKBOX,
			'checkbox_label' => 'Disable ads for logged in users',
			'description' => __('You can disable ads for all registered users (and administrators).', 'propeller-ads'),
		));

		$this->setting_helper->add_field(array(
			'section' => 'general',
			'id' => 'token',
			'title' => 'Token',
			'type' => Propeller_Ads_Settings_Helper::FIELD_TYPE_INPUT_TEXT,
			'size' => 32,
			'validate' => true,
			'description' => '<a href="' . $this->token_url() . '">' . __('Get or update token automatically', 'propeller-ads') . '</a>',
		));

		$zoneList = $this->zone_helper->get_publisher_zones_group_by_direction();

		foreach ($zoneList as $direction_name => $zones) {
			$this->setting_helper->add_section(array(
				'id' => $direction_name,
				'title' => Propeller_Ads_Zone_Helper::$direction_name_map[$direction_name],
			));

			$this->setting_helper->add_field(array(
				'section' => $direction_name,
				'id' => 'enabled',
				'title' => 'Activation',
				'type' => Propeller_Ads_Settings_Helper::FIELD_TYPE_CHECKBOX,
				'checkbox_label' => 'Allow ads on all pages',
			));

			$options = array();

			foreach ($zones as $zone) {
				$title = $zone['id'] . ' ';
				$title .= $zone['title'] ? $zone['title'] : $zone['name'];
				$title .= $zone['scheme'] ? ' - ' . $zone['scheme'] : '';

				$options[] = array(
					'value' => $zone['id'],
					'title' => $title,
				);
			}

			$this->setting_helper->add_field(array(
				'section' => $direction_name,
				'id' => 'zone_id',
				'options' => $options,
				'type' => Propeller_Ads_Settings_Helper::FIELD_TYPE_DROPDOWN,
			));
		}
	}

	/**
	 * Get url for getting Anti AdBlock token
	 *
	 * @return string
	 */
	public function token_url()
	{
		return $this->ssp_route . '/#/pub/sites/anti_adblock_token?return=' . base64_encode($this->plugin_url());
	}

	/**
	 * Gets plugin settings page
	 *
	 * @return string
	 */
	public function plugin_url()
	{
		return admin_url('admin.php?page=' . $this->plugin_name);
	}

	/**
	 * Update settings page after update publisher zone list
	 * Wordpress action hook (admin_init)
	 */
	public function redirect_after_update()
	{
		if (isset($_GET['update-publisher-zones'])) {
			$this->zone_helper->update_publisher_zones();
			Propeller_Ads_Messages::add_message('Cache was removed. Synchronization of new zones may process some time. Please, repeat this action in 10 minutes. Thank you.');
			wp_redirect($this->plugin_url());
			exit();
		}
	}

	/**
	 * Save publisher Anti AdBlock after redirect from SSP
	 * Wordpress action hook (admin_init)
	 */
	public function auto_save_publisher_token()
	{

		if (isset($_GET['propeller-ads-aab-token'])) {
			$oldToken = $this->setting_helper->get_anti_adblock_token();
			$newToken = $_GET['propeller-ads-aab-token'];

			if ($oldToken !== $newToken) {
				$this->setting_helper->set_field_value('general', 'token', $newToken);
				$this->setting_helper->clear_settings();
				$this->zone_helper->update_publisher_zones();
			}

			wp_redirect($this->plugin_url());
			exit();
		}
	}

	public function error_notices()
	{
		if (!$this->setting_helper->get_anti_adblock_token()): ?>
			<?php if(isset($_GET['page']) && $_GET['page'] === $this->plugin_name): ?>
				<div class="error notice">
					<p>Do you have a PropellerAds Publisher account? If not, <a
							href="https://propellerads.com/registration-publisher/"
							target="_blank"><strong>register one</strong></a> - it
						takes less than 3 minutes.</p>
				</div>
			<?php endif; ?>
			<div class="error notice">
				<p>PropellerAds plugin error. API token is missing. <a href="<?php echo $this->plugin_url() ?>">Fix
						this</a></p>
			</div>
		<?php endif;

		if (!$this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION, 'enabled') &&
			!$this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_ONCLICK, 'enabled') &&
			!$this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'enabled')): ?>
			<div class="notice-warning notice">
				<p>PropellerAds plugin warning. All ads directions are disabled. Deactivate plugin or <a
						href="<?php echo $this->plugin_url() ?>">fix this</a></p>
			</div>
		<?php endif;
	}

	public function action_save_publisher_token()
	{
		//Clear all POST date after save publisher token
		unset($_POST);
		$this->setting_helper->clear_settings();
		$this->zone_helper->update_publisher_zones();
	}

	public function action_in_plugin_update()
	{
		$wp_list_table = _get_list_table('WP_Plugins_List_Table');

		printf(
			'<tr class="plugin-update-tr"><td colspan="%s" class="plugin-update update-message notice inline notice-warning notice-alt"><div class="update-message"><h4 style="margin: 0; font-size: 14px;">%s</h4>%s</div></td></tr>',
			$wp_list_table->get_column_count(),
			'PropellerAds Official Plugin Update Info',
			'WARNING! This is a brand new PropellerAds plugin version and its not compatible with old one. You\'ll must to relogin to PropellerAds SSP via plugin\'s page.'
		);
	}

	/**
	 * Open session if it doesn`t start
	 */
	public function register_session()
	{
		if (!session_id()) {
			session_start();
		}
	}
}
