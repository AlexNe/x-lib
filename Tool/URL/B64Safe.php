<?php
/**
 * Example:
 *
 * class ClassName
 * {
 *      use X\Tool\urlSafe;
 *      public function Test($Data)
 *      {
 *          return $this->urlSafeB64Encode($Data);
 *      }
 * }
 */
namespace X\Tool\URL;

trait B64Safe {
	protected function urlSafeB64Encode($data) {return str_replace(['+', '/', '\r', '\n', '='], ['-', '_'], base64_encode($data));}

	protected function urlSafeB64Decode($b64) {return base64_decode(str_replace(['-', '_'], ['+', '/'], $b64));}
}
?>