<?php
namespace X\Social\VK;

class getServerTokenError extends \X\ETrace\System {}

class ServerToken extends VKAPI {

	/**
	 * @var string
	 */
	private $oauth_url = "https://oauth.vk.com/";

	/**
	 * @var mixed
	 */
	private $server_token;

	/**
	 * @param \X\Social\VK\Credentials $Credentials
	 */
	public function __construct($Credentials = null) {
		parent::__construct($Credentials);
		$this->request();
	}

	private function request() {
		$params["client_id"]     = $this->Credentials->get_client_id();
		$params["client_secret"] = $this->Credentials->get_client_secret();
		$params["grant_type"]    = "client_credentials";
		$params["v"]             = $this->Credentials->get_api_version();

		$data = $this->query($this->oauth_url . "access_token?", $params);
		if (isset($data["access_token"])) {
			$this->server_token = $data["access_token"];
		} else {
			throw new getServerTokenError("VK: get server access_token fail", 0, ["params" => $params, "data" => $data]);
		}
	}

	/**
	 * @return mixed
	 */
	public function get_token() {
		return $this->server_token;
	}

	/**
	 * @return mixed
	 */
	public function getCredentials() {
		return $this->Credentials;
	}
}

?>