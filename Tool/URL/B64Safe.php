<?php
namespace X\Tool\URL;

trait B64Safe {
	/**
	 * @param $data
	 */
	protected function urlSafeB64Encode($data) {return str_replace(['+', '/', '\r', '\n', '='], ['-', '_'], base64_encode($data));}

	/**
	 * @param $b64
	 */
	protected function urlSafeB64Decode($b64) {return base64_decode(str_replace(['-', '_'], ['+', '/'], $b64));}
}

/*
Example:

class ClassName
{
use X\Tool\urlSafe;

public function Test($Data)
{
return $this->urlSafeB64Encode($Data);
}
}

 */
?>