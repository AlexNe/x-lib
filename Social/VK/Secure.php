<?php
namespace X\Social\VK;

class Secure {
	/**
	 * @var mixed
	 */
	private $ServerToken;

	/**
	 * @param string $ServerToken
	 */
	public function __construct($ServerToken) {
		$this->server_token = $server_token;
	}

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
	/**
	 * @param int      $user_id
	 * @param unixtime $date_from
	 * @param unixtime $date_to
	 * @param int      $limit
	 */
	public function getSMSHistory($user_id, $date_from, $date_to, $limit = 1000) {
		# code...
	}

	# secure.sendSMSNotification
	# Отправляет SMS-уведомление на мобильный телефон пользователя.
	/**
	 * @param int    $user_id
	 * @param string $message
	 */
	public function sendSMSNotification($user_id, $message) {
		# code...
	}

	# secure.sendNotification
	# Отправляет уведомление пользователю.
	/**
	 * @param string $user_ids  - идентификаторы через зяпятую: "13б,4563465,2345245". Max 100 users
	 * @param int    $user_id
	 * @param string $message
	 */
	public function sendNotification($user_ids, $user_id, $message) {
		# code...
	}

	# secure.setCounter
	# Устанавливает счетчик, который выводится пользователю жирным шрифтом в левом меню.
	/**
	 * @param string $counters  - user_id1:counter1,user_id2:counter2. Max 200 items
	 * @param int    $user_id
	 * @param int    $counter
	 */
	public function setCounter($counters, $user_id, $counter) {
		# code...
	}

	# secure.setUserLevel
	# Устанавливает игровой уровень пользователя в приложении, который смогут увидеть его друзья.
	/**
	 * @param $levels    - user_id1:level1,user_id2:level2, пример: 66748:6,6492:2. Max 200 items
	 * @param $user_id
	 * @param $level
	 */
	public function setUserLevel($levels, $user_id, $level) {
		# code...
	}

	# secure.getUserLevel
	# Возвращает ранее выставленный игровой уровень одного или нескольких пользователей в приложении.
	/**
	 * @param string $user_ids - userid1,userid2,userid3,
	 */
	public function getUserLevel($user_ids) {
		# code...
	}

	# secure.addAppEvent
	# Добавляет информацию о достижениях пользователя в приложении.
	/**
	 * @param $user_id
	 * @param $activity_id
	 * @param $value
	 */
	public function addAppEvent($user_id, $activity_id, $value) {
		# code...
	}

	# secure.checkToken
	# Позволяет проверять валидность пользователя в IFrame, Flash и Standalone-приложениях с помощью передаваемого в приложения параметра access_token.
	/**
	 * @param $token
	 * @param $ip
	 */
	public function checkToken($token, $ip = null) {
		# code...

		# В случае успеха будет возвращен объект, содержащий следующие поля:
		#    success = 1
		#    user_id = идентификатор пользователя
		#    date = unixtime дата, когда access_token был сгенерирован
		#    expire = unixtime дата, когда access_token станет не валиден

	}
}
?>