<?php

/**
 * The public-facing functionality of the plugin.
 */
class Propeller_Ads_Public
{
	const MANIFEST_JSON = 'manifest.json';
	private static $pushNotificationDomains = array(
		'propu.sh',
		'epu.sh',
		'pushlum.com',
		'pushlommy.com',
	);
	/**
	 * @var      string $version The current version of this plugin.
	 */
	private $version;
	/**
	 * @var Propeller_Ads_Settings_Helper $setting_helper Settings helper instance
	 */
	private $setting_helper;
	/**
	 * @var Propeller_Ads_Zone_Helper $zone_helper Zone helper instance
	 */
	private $zone_helper;
	/**
	 * @var Propeller_Ads_Anti_Adblock $anti_adbock Ad-Block file helper
	 */
	private $anti_adbock;

	/**
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		$this->version = $version;
		$this->setting_helper = new Propeller_Ads_Settings_Helper($plugin_name);
		$this->anti_adblock = new Propeller_Ads_Anti_Adblock($plugin_name);
		$this->zone_helper = new Propeller_Ads_Zone_Helper($plugin_name);
	}

	/**
	 * Insert ad
	 */
	public function insert_script()
	{
		if (is_user_logged_in() && $this->setting_helper->get_field_value('general', 'logged_in_disabled')) {
			return;
		}

		$anti_adblock_token = $this->setting_helper->get_anti_adblock_token();

		if ($this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_ONCLICK, 'enabled')) {
			$onclick_zone_id = $this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_ONCLICK, 'zone_id');

			if ($anti_adblock_token && $onclick_zone_id && $this->zone_helper->is_anti_adblock_zone($onclick_zone_id)) {
				echo $this->anti_adblock->get($onclick_zone_id);
			} else {
				echo $this->get_standard_script(Propeller_Ads_Zone_Helper::DIRECTION_ONCLICK);
			}
		}

		if ($this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_INTERSTITIAL, 'enabled')) {
			echo $this->get_standard_script(Propeller_Ads_Zone_Helper::DIRECTION_INTERSTITIAL);
		}

		if ($this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION, 'enabled')) {
			$pushnotification_zone_id = $this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION, 'zone_id');

			if ($anti_adblock_token && $pushnotification_zone_id && $this->zone_helper->is_anti_adblock_zone($pushnotification_zone_id)) {
				$this->anti_adblock->getServiceWorkerFile($pushnotification_zone_id);
				$this->anti_adblock->getManifestFile();
				echo $this->anti_adblock->get($pushnotification_zone_id);
			} else {
				echo $this->get_standard_script(Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION);
			}
		}
	}

	/**
	 * Get standard tag
	 *
	 * @param   string $format
	 * @return  string
	 */
	private function get_standard_script($format)
	{
		$is_enabled = $this->setting_helper->get_field_value($format, 'enabled');
		$zone_id = $this->setting_helper->get_field_value($format, 'zone_id');

		if ($is_enabled && !empty($zone_id)) {
			return sprintf($this->get_standard_script_template($format), $zone_id);
		}

		return '';
	}

	/**
	 * Get template for standard tag
	 *
	 * @param string $format
	 * @return string
	 */
	private function get_standard_script_template($format)
	{
		switch ($format) {
			case Propeller_Ads_Zone_Helper::DIRECTION_ONCLICK:
				return '<script data-cfasync="false" type="text/javascript" src="//bodelen.com/apu.php?zoneid=%d"></script>';

			case Propeller_Ads_Zone_Helper::DIRECTION_INTERSTITIAL:
				return '<script data-cfasync="false" type="text/javascript" src="//tharbadir.com/notice.php?p=%d&interstitial=1"></script>';

			case Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION:
				return '<script data-cfasync="false" type="text/javascript" src="//' . self::$pushNotificationDomains[array_rand(self::$pushNotificationDomains)] . '/ntfc.php?p=%d" async="async"></script>';
		}

		return '';
	}

	/**
	 * Insert manifest.json relation link in <head>
	 */
	public function insert_manifest()
	{
		if ($this->setting_helper->get_field_value(Propeller_Ads_Zone_Helper::DIRECTION_PUSHNOTIFICATION, 'enabled')) {
			echo '<link rel="manifest" href="' . get_site_url() . '/' . self::MANIFEST_JSON . '">' . PHP_EOL;
		}
	}
}
