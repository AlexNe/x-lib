<?php
namespace X\Security\Crypto;
class IDEA {
	// protected function strToHex($string)
	// protected function hexToStr($hex)
	use \X\Tool\Strings\CharHEX;
	// protected function urlSafeB64Encode
	// protected function urlSafeB64Decode
	use \X\Tool\URL\B64Safe;

	/**
	 * @var hex
	 */
	protected $key, $IV;

	/**
	 * @var mixed
	 */
	private $crypt_algo;
	/**
	 * @param string $key
	 */
	public function __construct($key, $Algo = "idea-ecb") {
		$this->setKey($key);
		$this->setAlgo($Algo);
	}

	/**
	 * @param string $Key
	 */
	public function setKey($Key) {
		$this->key = $this->strToHex($Key);
	}

	/**
	 * @param string $IV
	 */
	public function setIV($IV) {
		$this->IV = $this->strToHex($IV);
	}

	/**
	 * @param string $Algo
	 */
	public function setAlgo($Algo) {
		if (in_array($Algo, openssl_get_cipher_methods())) {
			$this->crypt_algo = $Algo;
		} else {
			throw new \X\ETrace\System("Crypt Algorithm not found: " . $Algo, 0, ["allow_algo" => openssl_get_cipher_methods()]);
		}
	}

	/**
	 * @param $Data
	 */
	protected function crypt_bin($Data) // : string // BIN
	{
		return openssl_encrypt($Data, $this->crypt_algo, $this->key, OPENSSL_RAW_DATA, $this->IV);
	}

	/**
	 * @param $BIN
	 */
	protected function decrypt_bin($BIN) // : Array
	{
		return openssl_decrypt($BIN, $this->crypt_algo, $this->key, OPENSSL_RAW_DATA, $this->IV);
	}

	/**
	 * @param $Data
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
	 * @param  $Data
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
}
?>