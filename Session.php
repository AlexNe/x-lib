<?php
class X_Session {
	// protected function urlSafeB64Encode
	// protected function urlSafeB64Decode
	use X\Tool\URL\B64Safe;

	/**
	 * @var mixed
	 */
	protected $key, $algo, $session = false, $IV = null;
	/**
	 * @var string
	 */
	protected $collName = "s";

	public function __construct() {
		$this->setKey("12345678"); // 8 symbols
		$this->setAlgo("idea-ecb");
	}

	/**
	 * @param $Key
	 */
	public function setKey($Key) {
		$this->key = $this->strToHex($Key);
	}

	/**
	 * @param $IV
	 */
	public function setIV($IV) {
		$this->IV = $this->strToHex($IV);
	}

	/**
	 * @param $Algo
	 */
	public function setAlgo($Algo) {
		if (in_array($Algo, openssl_get_cipher_methods())) {
			$this->algo = $Algo;
		} else {
			throw new Exception("Crypt Algorithm not found: " . $Algo, 0);
		}
	}

	/**
	 * @param  $Data
	 * @param  $algo
	 * @return mixed
	 */
	protected function make_session($Data, $algo = "b64") {
		switch ($algo) {
			case 'b64':
				return $this->session = $this->crypt_b64($Data);
				break;

			case 'hex':
				return $this->session = $this->crypt_hex($Data);
				break;

			case 'bin':
				return $this->session = $this->crypt_bin($Data);
				break;

			default:
				return $this->session = $this->crypt_bin($Data);
				break;
		}
	}

	/**
	 * @param array   $Data
	 * @param $algo
	 */
	public function set_cookie($Data, $algo = "b64") // : void // php7
	{
		setcookie($this->collName,
			$this->make_session($Data, $algo),
			time() + (60 * 60 * 24 * 30 * 12 * 10), ////////////////////////////////// TIME LIVE COOKIE 10 years
			"/"
		);
	}

	/**
	 * @param array $Data
	 */
	protected function crypt_bin($Data) // : string // BIN
	{
		return openssl_encrypt(implode(":", $Data), $this->algo, $this->key, OPENSSL_RAW_DATA, $this->IV);
	}

	/**
	 * @param $BIN
	 */
	protected function decrypt_bin($BIN) // : Array
	{
		return explode(":", openssl_decrypt($BIN, $this->algo, $this->key, OPENSSL_RAW_DATA, $this->IV));
	}

	/**
	 * @param array $Data
	 */
	protected function crypt_hex($Data) // : string // HEX
	{
		return bin2hex($this->crypt_bin($Data));
	}

	/**
	 * @param  $HEX
	 * @return mixed
	 */
	protected function decrypt_hex($HEX) // : Array
	{
		return $this->decrypt_bin(hex2bin($HEX));
	}

	/**
	 * @param  array   $Data
	 * @return mixed
	 */
	protected function crypt_b64($Data) // : string // Base64
	{
		return $this->urlSafeB64Encode($this->crypt_bin($Data));
	}

	/**
	 * @param  $B64
	 * @return mixed
	 */
	protected function decrypt_b64($B64) // : Array
	{
		return $this->decrypt_bin($this->urlSafeB64Decode($B64));
	}

	/**
	 * @param  $string
	 * @return mixed
	 */
	private function strToHex($string) {
		$hex = "";
		for ($i = 0; $i < strlen($string); $i++) {$hex .= dechex(ord($string[$i]));}
		return $hex;
	}

	/**
	 * @param  $hex
	 * @return mixed
	 */
	private function hexToStr($hex) {
		$string = "";
		for ($i = 0; $i < strlen($hex) - 1; $i += 2) {$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));}
		return $string;
	}
}
?>