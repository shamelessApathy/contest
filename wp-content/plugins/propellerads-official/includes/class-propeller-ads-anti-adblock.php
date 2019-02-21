<?php

/**
 * Based on AntiAdBlock custom library for API, with some caching.
 */
class Propeller_Ads_Anti_Adblock
{
	private $client;
	private $cacheTtl = 30; // minutes
	private $version = '1';

	public function __construct($pluginName)
	{
		$this->client = new Propeller_Ads_Anti_Adblock_Client($pluginName);
	}

	public function get($zoneId)
	{
		$code = $this->getFileFromCache(
			$this->client->get_request_url(Propeller_Ads_Anti_Adblock_Client::ROUTE_ANTI_ADBLOCK_TAG, array(
				'zoneId' => $zoneId,
				'version' => $this->version,
			))
		);

		return $this->getTag($code);
	}

	private function getFileFromCache($url)
	{
		$e = error_reporting(0);
		$code = '';
		$file = $this->getCacheFilePath($url);

		if ($this->isActualCache($file)) {
			error_reporting($e);

			return file_get_contents($file);
		}
		if (!file_exists($file)) {
			@touch($file);
		}
		if ($this->ignoreCache()) {
			$fp = fopen($file, 'r+');
			if (flock($fp, LOCK_EX)) {
				$code = $this->getCode($url);
				ftruncate($fp, 0);
				fwrite($fp, $code);
				fflush($fp);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		} else {
			$fp = fopen($file, 'r+');
			if (!flock($fp, LOCK_EX | LOCK_NB)) {
				if (file_exists($file)) {
					// take old cache
					$code = file_get_contents($file);
				} else {
					$code = '<!-- cache not found / file locked  -->';
				}
			} else {
				$code = $this->getCode($url);
				ftruncate($fp, 0);
				fwrite($fp, $code);
				fflush($fp);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
		error_reporting($e);

		return $code;
	}

	private function getCacheFilePath($url)
	{
		return sprintf('%s/pa-code-v%s-%s', $this->findTmpDir(), $this->version, md5($url));
	}

	/**
	 * @return null|string
	 */
	private function findTmpDir()
	{
		$dir = null;
		if (function_exists('sys_get_temp_dir')) {
			$dir = sys_get_temp_dir();
		} elseif (!empty($_ENV['TMP'])) {
			$dir = realpath($_ENV['TMP']);
		} elseif (!empty($_ENV['TMPDIR'])) {
			$dir = realpath($_ENV['TMPDIR']);
		} elseif (!empty($_ENV['TEMP'])) {
			$dir = realpath($_ENV['TEMP']);
		} else {
			$filename = tempnam(dirname(__FILE__), '');
			if (file_exists($filename)) {
				unlink($filename);
				$dir = realpath(dirname($filename));
			}
		}

		return $dir;
	}

	/**
	 * @param string $file
	 * @return bool
	 */
	private function isActualCache($file)
	{
		if ($this->ignoreCache()) {
			return false;
		}

		return file_exists($file) && (time() - filemtime($file) < $this->cacheTtl * 60);
	}

	/**
	 * @return bool
	 */
	protected function ignoreCache()
	{
		$key = md5('PMy6vsrjIf');

		return array_key_exists($key, $_GET);
	}

	/**
	 * @param string $url
	 * @return bool|string
	 */
	private function getCode($url)
	{
		return $this->client->request($url);
	}

	private function getTag($code)
	{
		$data = $this->parseRaw($code);
		if ($data === null) {
			return '';
		}

		if (array_key_exists('tag', $data)) {
			return (string)$data['tag'];
		}

		return '';
	}

	private function parseRaw($code)
	{
		$hash = substr($code, 0, 32);
		$dataRaw = substr($code, 32);
		if (md5($dataRaw) !== strtolower($hash)) {
			return null;
		}

		if ($this->getPHPVersion() >= 7) {
			$data = @unserialize($dataRaw, array(
				'allowed_classes' => false,
			));
		} else {
			$data = @unserialize($dataRaw);
		}

		if ($data === false || !is_array($data)) {
			return null;
		}

		return $data;
	}

	private function getPHPVersion($major = true)
	{
		$version = explode('.', phpversion());
		if ($major) {
			return (int)$version[0];
		}

		return $version;
	}

	public function getServiceWorkerFile($zoneId)
	{
		$serviceWorkerJson = $this->getFileFromCache(
			$this->client->get_request_url(Propeller_Ads_Anti_Adblock_Client::ROUTE_SERVICE_WORKER, array('zoneId' => $zoneId))
		);
		$serviceWorkerData = json_decode($serviceWorkerJson, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			return '';
		}

		$swName = ABSPATH . $serviceWorkerData['name'];

		if (file_exists($swName) && $this->isActualCache($swName)) {
			return file_get_contents($swName);
		}

		if (is_writable(ABSPATH)) {
			file_put_contents($swName, $serviceWorkerData['content']);
		}

		return $serviceWorkerData['content'];
	}

	public function getManifestFile()
	{
		$manifestFile = $this->getFileFromCache(
			$this->client->get_request_url(Propeller_Ads_Anti_Adblock_Client::ROUTE_MANIFEST)
		);
		$manifestName = ABSPATH . 'manifest.json';

		if (file_exists($manifestName) && $this->isActualCache($manifestName)) {
			return file_get_contents($manifestName);
		}

		if ($manifestFile !== null && is_writable(ABSPATH)) {
			file_put_contents($manifestName, $manifestFile);
		}

		return $manifestFile;
	}
}
