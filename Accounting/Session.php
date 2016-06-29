<?php
namespace X\Accounting;
class SessionChecksumError extends \X\ETrace\Notification {}
class SessionHacked extends \X\ETrace\Notification {}

/**
 * Session Manager
 *
 * 	Session struct:
 *  	t 	= type(int)
 *  	s 	= session_id
 *  	cc 	= crypto code (спец код с возможностю прокрутки назад и вперед.)
 *  	cs 	= crypto cheksum
 *  	a 	= activation_time
 *  	u 	= user_id
 *  	hs 	= is_https(bool)
 *
 * 	crypto cheksum:
 * 		cheksum = ((( a & ( ! s ) ) | cc ) ^ u ) >> (t ^ op1)
 *
 * 	crypto code:
 * 		code = rand(0,time());
 * 		next_code = (code >> op2) ^ op3
 * 		prev_code =	(code ^ op3) << op2
 */
class Session extends \X\Security\Crypto\IDEA {
	//protected function BitwiseCROR($v, $c)
	//protected function BitwiseCROL($v, $c)
	use \X\Tool\BitwiseCyclicShift;
	/**
	 * @var array
	 */
	protected $session_data = null;
	/**
	 * @var mixed
	 */
	protected $session = null;
	/**
	 * @var string
	 */
	protected $session_name = "s";

	/**
	 * @var array
	 */
	protected $param_crypto = [2, 6, 4];

	/**
	 * @param string $key          - password
	 * @param string $name         - collumn name
	 * @param array  $param_crypto - [int op1, int op2, int op3]
	 */
	public function __construct($key, $name = "s", $param_crypto = false) {
		parent::__construct($key);
		if ($param_crypto) {
			$this->param_crypto = $param_crypto;
		}
		$this->session_name = $name;
	}

	protected function read_session() {
		$In            = new \X_Input();
		$this->session = $In->CookieValue($this->session_name, false) ?: $In->Request($this->session_name, "")->string();
		if (strlen($this->session) > 0) {
			if (is_array($session_data = $this->explode(gzuncompress($this->decrypt_b64($this->session))))) {
				if (isset($session_data["cs"])) {
					if ($session_data["cs"] == $this->crypto_checksum($session_data)) {
						$this->session_data = $session_data;
						return true;
					} else {
						throw new SessionChecksumError("Checksum Error", [get_defined_vars(), $this]);
					}
				}
			}
		}
		return false;
	}

	/**
	 * @param $session_data
	 */
	protected function make_session($session_data) {
		if ( ! isset($session_data["cc"])) {
			$session_data["cc"] = $this->crypto_code_new();
		}
		$session_data["cs"]   = $this->crypto_checksum($session_data);
		$this->session_data   = $session_data;
		return $this->session = $this->crypt_b64(gzcompress($this->implode($session_data)));
	}

	/**
	 * @param $D
	 */
	protected function check_data_colls($D) {
		if (isset($D["a"]) && isset($D["s"]) && isset($D["cc"]) && isset($D["u"]) && isset($D["t"])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $code_session
	 * @param $code_base
	 * @param $depth
	 */
	protected function crypto_code_check($code_session, $code_base, $depth = 3) {
		for ($i = 0; $i < $depth; $i++) {
			if ($code_session == $code_base) {
				return true;
			}
			$code_base = $this->crypto_code_prev($code_base);
		}
		return false;
	}

	protected function crypto_code_new() {
		return rand(1000, time());
	}

	/**
	 * @param $code
	 */
	protected function crypto_code_next($code) {
		return $this->BitwiseCROR($code, $this->param_crypto[1]) ^ $this->param_crypto[2];
	}

	/**
	 * @param $code
	 */
	protected function crypto_code_prev($code) {
		return $this->BitwiseCROL(($code ^ $this->param_crypto[2]), $this->param_crypto[1]);
	}

	/**
	 * @return mixed
	 */
	protected function crypto_checksum($session_data) {
		if (is_array($session_data)) {
			if ( ! $this->check_data_colls($session_data)) {
				throw new SessionHacked("Session data not full!", $session_data);
			}
			$D = array_map(function ($i) {return intval($i);}, $session_data);
			return $this->BitwiseCROR(((($D["a"] & ( ! $D["s"])) | $D["cc"]) ^ $D["u"]), ($D["t"] ^ $this->param_crypto[0]));
		}
	}

	/**
	 * @param $Data
	 */
	protected function set_cookie($Data) {
		setcookie($this->session_name,
			$this->make_session($Data),
			time() + (60 * 60 * 24 * 30 * 12 * 10), ////////////////////////////////// TIME LIVE COOKIE 10 years
			"/"
		);
	}

	/**
	 * @param $data
	 */
	protected function implode($data) {
		array_walk($data, function (&$i, $k) {$i = implode(":", [$k, $i]);});
		return implode(";", $data);
	}

	/**
	 * @param  $string
	 * @return mixed
	 */
	protected function explode($string) {
		$data_t = explode(";", $string);
		$data   = [];
		foreach ($data_t as $value) {
			list($k, $i) = explode(":", $value);
			$data[$k]    = $i;
		}
		return $data;
	}
}
/**
 * EXAMPLE:
 *
 * class Session extends X\Accounting\Session
 * {
 * 	public __construct()
 * 	{
 * 		parent::__construct(Config::KEY, Config::NAME, Config::CRYPTO);
 * 	}
 * }
 *
 *
 */
?>