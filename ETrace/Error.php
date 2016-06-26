<?php
namespace X\ETrace;

class Error extends TItem
{

	/**
	 * @param string $message
	 * @param int    $code      [= 0]
	 * @param string $file      [= false]
	 * @param int    $line      [= false]
	 * @param array  $context   [= []]
	 */
	public function __construct($message, $code = 0, $file = false, $line = false, $context = [])
	{
		parent::__construct("error", $message, $code, $file, $line, $context);
	}
}
?>