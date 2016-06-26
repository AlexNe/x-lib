<?php
namespace X\ETrace;
class Trace extends EItem
{
	/**
	 * Упрощенный интерфейс для отладки кода, проверки стека и переменных окружения.
	 *
	 * @param $message
	 * @param array      $context
	 */
	public function __construct($message, $context = [])
	{
		parent::__construct("trace", $message, 0, false, false, $context);
	}

}
?>
