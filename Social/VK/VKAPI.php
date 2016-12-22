<?php
namespace X\Social\VK;
use X\ETrace\System as ETSystem;
use X\Network\Http\Client as HttpClient;

class VKAPICredentialsIsNull extends ETSystem {}
class VKAPIResponseError extends ETSystem {}
class VKAPIResponseNull extends ETSystem {}
class VKAPI {
	/**
	 * @var \X\Social\VK\Credentials
	 */
	static $GlobalCredentials = null;

	/**
	 * @var \X\Social\VK\Credentials
	 */
	protected $Credentials;

	/**
	 * @var string
	 */
	protected $api_url = "https://api.vk.com/";

	/**
	 * @param \X\Social\VK\Credentials $Credentials
	 */
	public function __construct($Credentials = null) {
		if (is_null($Credentials)) {
			$this->Credentials = self::$GlobalCredentials;
		} else {
			$this->Credentials = $Credentials;
		}

		if (is_null($this->Credentials)) {
			throw new VKAPICredentialsIsNull("VK Credentials is NULL", 0);
		}
	}

	/**
	 * @param  string  $method
	 * @param  array   $params
	 * @return array
	 */
	public function api($method, $params = []) {
		if ( ! isset($params['v'])) {
			$params["v"] = "5.28";
		}

		return $this->query($this->api_url . "method/" . $method, $params);
	}

	/**
	 * @param  $url
	 * @param  $params
	 * @return array
	 */
	protected function query($url, $params = []) {
		$Client = new HttpClient($url);
		$Client->set_model_data(["post" => $params]);
		$ClientResponse = $Client->exec();
		if ($data = $ClientResponse->json_decode()) {
			return $data;
		} #else {
		#return $ClientResponse->get_body();
		#}
		return false;
	}

	protected function check_response($data) {
		if (isset($data["response"])) {
			return $data["response"];
		}

		if (isset($data["error"])) {
			throw new VKAPIResponseError($data["error"]["msg"], $data["error"]["code"]);
		}

		throw new VKAPIResponseNull("VK API response error", 1, ["vk_response" => $data]);
	}
}
?>