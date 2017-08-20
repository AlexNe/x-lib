<?php
namespace X\Tool\Strings;
trait CharHEX {
	protected function strToHex($string) {
		$hex = "";
		for ($i = 0; $i < strlen($string); $i++) {$hex .= dechex(ord($string[$i]));}
		return $hex;
	}

	protected function hexToStr($hex) {
		$string = "";
		for ($i = 0; $i < strlen($hex) - 1; $i += 2) {$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));}
		return $string;
	}
}
?>