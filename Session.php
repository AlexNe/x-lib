<?php
class X_Session
{
	use X\Tool\urlSafe;

	protected $key, $algo, $session = false, $IV = null;
	protected $collName = "s";

	function __construct()
	{
		$this->setKey("12345678"); // 8 symbols
		$this->setAlgo("idea-ecb");
	}

	public function setKey($Key)
	{
		$this->key = $this->strToHex($Key);
	}
	public function setIV($IV)
	{
		$this->IV = $this->strToHex($IV);
	}

	public function setAlgo($Algo)
	{
		if(in_array($Algo, openssl_get_cipher_methods()))
			$this->algo = $Algo;
		else throw new Exception("Crypt Algorithm not found: ".$Algo, 0);
		
	}

	protected function make_session($Data, $algo = "b64")
	{
		switch ($algo) 
		{
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

	public function set_cookie(Array $Data, $algo = "b64") // : void // php7
	{
		setcookie($this->collName,
			$this->make_session($Data, $algo),
			time()+(60*60*24*30*12*10), ////////////////////////////////// TIME LIVE COOKIE 10 years
			"/"
		);
	}

	protected function crypt_bin(Array $Data) // : string // BIN
	{
		return openssl_encrypt(implode(":", $Data), $this->algo, $this->key, OPENSSL_RAW_DATA, $this->IV );
	}

	protected function decrypt_bin($BIN) // : Array
	{
		return explode(":", openssl_decrypt($BIN, $this->algo, $this->key, OPENSSL_RAW_DATA, $this->IV ));
	}

	protected function crypt_hex(Array $Data) // : string // HEX
	{
		return bin2hex( $this->crypt_bin($Data) );
	}

	protected function decrypt_hex($HEX) // : Array
	{
		return $this->decrypt_bin( hex2bin($HEX) );
	}

	protected function crypt_b64(Array $Data) // : string // Base64
	{
		return $this->urlSafeB64Encode( $this->crypt_bin($Data) );
	}

	protected function decrypt_b64($B64) // : Array
	{
		return $this->decrypt_bin( $this->urlSafeB64Decode($B64) );
	}


	private function strToHex($string)
	{
		$hex = "";
		for ($i=0; $i < strlen($string); $i++){ $hex .= dechex(ord($string[$i])); }
		return $hex;
	}

	private function hexToStr($hex)
	{
		$string = "";
		for ($i=0; $i < strlen($hex)-1; $i+=2){ $string .= chr(hexdec($hex[$i].$hex[$i+1])); }
		return $string;
	}
}
?>