<?php
class X_Session
{
	protected $key, $algo;

	function __construct()
	{
		$this->setKey("12345678"); // 8 symbols
		$this->setAlgo("idea-ecb");
	}

	public function setKey($Key)
	{
		$this->key = $this->strToHex($Key);
	}
	public function setAlgo($Algo)
	{
		if(in_array($Algo, openssl_get_cipher_methods()))
			$this->algo = $Algo;
		else throw new Exception("Crypt Algorithm not found: ".$Algo, 0);
		
	}

	protected function crypt(Array $Data) // : string // HEX
	{
		return bin2hex(openssl_encrypt(implode(":", $Data), $this->algo, $this->key, OPENSSL_RAW_DATA ));
	}

	protected function decrypt($HEX) // : Array
	{
		return explode(":", openssl_decrypt(hex2bin($HEX), $this->algo, $this->key, OPENSSL_RAW_DATA ));
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