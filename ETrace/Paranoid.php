<?php
namespace X\ETrace;
class Paranoid extends EItem
{
	/**
	 * Параноидальный лог. Нужен, что бы не сыпался в основной лог, но с возможностю отслежки недостатков кода.
	 *
	 * @param $message
	 * @param array      $context
	 */
	public function __construct($message, $code = 0, $context = [])
	{
		parent::__construct("paranoid", $message, $code, false, false, $context);
	}

}
?>