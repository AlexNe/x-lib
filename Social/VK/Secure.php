<?php
namespace X\Social\VK;
class VKSecureNullToken extends \X\ETrace\System {}
class VKSecureNullVarible extends \X\ETrace\System {}

class Secure extends VKAPI {
	/**
	 * @var mixed
	 */
	private $ServerToken;

	/**
	 * @param \X\Social\VK\ServerToken $server_token
	 */
	public function __construct($server_token) {
		$this->server_token = $server_token;
		if (is_null($server_token)) {
			throw new VKSecureNullToken("Client access_token is null", 0);
		}
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
	 * @param string $access_token - клиентский access_token
	 * @param string $ip           - ip адрес пользователя. Если параметр не передан – ip адрес проверен не будет.
	 */
	public function checkToken($access_token, $ip = null) {
		if (is_null($access_token) || strlen($access_token) == 0) {
			throw new VKSecureNullVarible("Value 'access_token' is null", 0);
		}
		$params["token"]         = $access_token;
		$params["access_token"]  = $this->server_token->get_token();
		$params["client_secret"] = $this->server_token->getCredentials()->get_client_secret();
		$params["v"]             = $this->server_token->getCredentials()->get_api_version();
		$data                    = $this->query($this->api_url . "method/secure.checkToken?", $params);
		# В случае успеха будет возвращен объект, содержащий следующие поля:
		#    success = 1
		#    user_id = идентификатор пользователя
		#    date = unixtime дата, когда access_token был сгенерирован
		#    expire = unixtime дата, когда access_token станет не валиден
		if (isset($data["response"]["success"])) {
			return $data["response"];
		} else if (isset($data["error"])) {
			throw new VKAPIResponseError($data["error"]["error_msg"],
				$data["error"]["error_code"],
				["params" => $params, "request_params" => $data["error"]["request_params"]]);
		} else {
			throw new VKAPIResponseNull("VK API return null response", 0, ["params" => $params, "data" => $data]);
		}
	}
}
?>