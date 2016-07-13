<?php
namespace X\Social\VK;

class Credentials {
	/**
	 * @var mixed
	 */
	private $client_id, $client_secret, $api_version;
	/**
	 * @param $client_id
	 * @param $client_secret
	 */
	public function __construct($client_id, $client_secret, $api_version = "5.28") {
		if (is_numeric($client_id) && $client_id > 0) {
			$this->client_id = $client_id;
		} else {
			throw new \X\ETrace\System("VK Client ID not numeric or not > 0", 0, ["client_id" => $client_id]);
		}

		if (is_string($client_secret) && strlen($client_secret) > 0) {
			$this->client_secret = $client_secret;
		} else {
			throw new \X\ETrace\System("VK Client Secret not string or len =< 0", 0, ["client_secret" => $client_secret]);
		}
		$this->api_version = $api_version;
	}

	/**
	 * @return int
	 */
	public function get_client_id() {
		return $this->client_id;
	}

	/**
	 * @return string
	 */
	public function get_client_secret() {
		return $this->client_secret;
	}

	/**
	 * @return string
	 */
	public function get_api_version() {
		return $this->api_version;
	}
}
?>