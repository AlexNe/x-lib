<?php
namespace X\Social\VK;
class VKAPICredentialsIsNull extends \X\ETrace\System {}
class VKAPIResponseError extends \X\ETrace\System {}
class VKAPIResponseNull extends \X\ETrace\System {}

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
	public function api($method, $params = []) {}

	/**
	 * @param  $url
	 * @param  $params
	 * @return array
	 */
	protected function query($url, $params = []) {
		$Client = new \X\Network\Http\Client($url);
		$Client->set_model_data(["post" => $params]);
		if ($data = $Client->exec()->json_decode()) {
			return $data;
		}
		return;
	}
}
?>