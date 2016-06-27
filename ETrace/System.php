<?php
namespace X\ETrace;
class System extends EItem {
	/**
	 * @param $message
	 * @param $code
	 * @param array      $context
	 */
	public function __construct($message, $code = 1, $context = []) {
		parent::__construct("system", $message, $code, false, false, $context);
	}
}
?>