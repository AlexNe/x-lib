<?php
namespace X\ETrace;
class Notification extends EItem {
	/**
	 * Упрощенный интерфейс для посылки сообщений администратору из неких участков кода.
	 *
	 * @param $message
	 * @param array      $context
	 */
	public function __construct($message, $context = []) {
		parent::__construct("notification", $message, 0, false, false, $context);
	}
}
?>
