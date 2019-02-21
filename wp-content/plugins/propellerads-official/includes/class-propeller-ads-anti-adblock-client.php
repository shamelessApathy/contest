<?php

class Propeller_Ads_Anti_Adblock_Client
{
	const ROUTE_PUBLISHER_ZONES = 'v3/getPublisherZones';
	const ROUTE_SERVICE_WORKER = 'v3/getServiceWorker';
	const ROUTE_MANIFEST = 'v3/getManifest';
	const ROUTE_ANTI_ADBLOCK_TAG = 'v3/getTag';

	/**
	 * Adp Anti AdBlock url
	 *
	 * @var string
	 */
	private $requestDomainName = 'go.transferzenad.com';

	/**
	 * @var Propeller_Ads_Settings_Helper $setting_helper Settings helper instance
	 */
	private $settings_helper;

	public function __construct($plugin_name)
	{
		$this->settings_helper = new Propeller_Ads_Settings_Helper($plugin_name);
	}

	/**
	 * Gets all publisher zones by token
	 *
	 * @return array|null
	 */
	public function get_publisher_zones()
	{
		return $this->request(
			$this->get_request_url(self::ROUTE_PUBLISHER_ZONES),
			true
		);
	}

	public function get_request_url($endpoint, $params = array())
	{
		$params['token'] = $this->settings_helper->get_anti_adblock_token();

		if (!$params['token']) {
			return null;
		}

		$requestUrl = 'http://' . $this->requestDomainName . '/' . $endpoint . '?' . http_build_query($params);

		return $requestUrl;
	}

	public function request($url, $decode = false)
	{
		if ($url === null) {
			return null;
		}

		$response = wp_remote_get($url);

		if (is_array($response)) {
			if ($decode) {
				$decodedData = json_decode($response['body'], true);

				if (json_last_error() === JSON_ERROR_NONE) {
					return $decodedData;
				}

				return null;
			}

			return $response['body'];
		}

		return null;
	}
}
