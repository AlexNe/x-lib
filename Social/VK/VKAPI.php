<?php
namespace X\Social\VK;

class VKAPI {

	/**
	 * @var \X\Social\VK\Credentials
	 */
	static $GlobalCredentials = null;

	/**
	 * @var \X\Social\VK\Credentials
	 */
	private $Credentials;

	/**
	 * @var string
	 */
	private $api_url;

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
			throw new \X\ETrace\System("VK Credentials is NULL", 0);
		}
	}

	/**
	 * @param  string  $method
	 * @param  array   $params
	 * @return array
	 */
	public function api($method, $params = []) {

		$Client = new \X\Network\Http\Client($this->api_url . "/" . $method);
		$Client->set_model_data(["post" => $params]);
		if ($data = $Client->exec()->json_decode()) {
			return $data;
		}
		return;
	}
}
?>