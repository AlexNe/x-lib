<?php
namespace X\Social\VK;

class Secure {

	/**
	 * @param \X\Social\VK\Credentials $Credentials
	 */
	public function __construct($Credentials = null) {}

	# secure.getAppBalance
	# Возвращает платежный баланс (счет) приложения в сотых долях голоса.
	public function getAppBalance() {
		# code...
	}

	# secure.getTransactionsHistory
	# Выводит историю транзакций по переводу голосов между пользователями и приложением.
	public function getTransactionsHistory() {
		# code...
	}

	# secure.getSMSHistory
	# Выводит список SMS-уведомлений, отосланных приложением с помощью метода secure.sendSMSNotification.
	public function getSMSHistory() {
		# code...
	}

	# secure.sendSMSNotification
	# Отправляет SMS-уведомление на мобильный телефон пользователя.
	public function sendSMSNotification() {
		# code...
	}

	# secure.sendNotification
	# Отправляет уведомление пользователю.
	public function sendNotification() {
		# code...
	}

	# secure.setCounter
	# Устанавливает счетчик, который выводится пользователю жирным шрифтом в левом меню.
	public function setCounter() {
		# code...
	}

	# secure.setUserLevel
	# Устанавливает игровой уровень пользователя в приложении, который смогут увидеть его друзья.
	public function setUserLevel() {
		# code...
	}

	# secure.getUserLevel
	# Возвращает ранее выставленный игровой уровень одного или нескольких пользователей в приложении.
	public function getUserLevel() {
		# code...
	}

	# secure.addAppEvent
	# Добавляет информацию о достижениях пользователя в приложении.
	public function addAppEvent() {
		# code...
	}

	# secure.checkToken
	# Позволяет проверять валидность пользователя в IFrame, Flash и Standalone-приложениях с помощью передаваемого в приложения параметра access_token.
	public function checkToken() {
		# code...
	}
}
?>