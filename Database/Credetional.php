<?php
namespace X\Database;
use \X\ETrace\System as ESys;

class Credetional_ConfigurationArgumentsError extends ESys {}

class Credetional {
	/**
	 * @var string
	 */
	protected $hostname = "localhost";
	/**
	 * @var string
	 */
	protected $username = "root";
	/**
	 * @var string
	 */
	protected $password = "";
	/**
	 * @var string
	 */
	protected $database = "test";
	/**
	 * @var string
	 */
	protected $charset = "utf8";
	/**
	 * @var string
	 */
	protected $port = "3306";

	# new Credetional(<database>)
	# new Credetional(<username>,<password>,<database>)
	# new Credetional(<hostname>,<username>,<password>,<database>)
	# new Credetional(<hostname>,<username>,<password>,<database>,<charset>)
	public function __construct() {
		$data = func_get_args();
		switch (count($data)) {
			case 1:
				$this->database = $data[0];
				break;
			case 3:
				$this->username = $data[0];
				$this->password = $data[1];
				$this->database = $data[2];
				break;

			case 4:
				$this->hostname = $data[0];
				$this->username = $data[1];
				$this->password = $data[2];
				$this->database = $data[3];
				break;
			case 5:
				$this->hostname = $data[0];
				$this->username = $data[1];
				$this->password = $data[2];
				$this->database = $data[3];
				$this->charset  = $data[4];
				break;
			case 6:
				$this->hostname = $data[0];
				$this->username = $data[1];
				$this->password = $data[2];
				$this->database = $data[3];
				$this->charset  = $data[4];
				$this->port     = $data[5];
				break;

			default:
				throw new Credetional_ConfigurationArgumentsError("Configuration Arguments Error", 1, ["args" => $data]);
				break;
		}
	}

	public function get_PDO_MySQL_DSN() {
		return "mysql:host={$this->hostname};port={$this->port};dbname={$this->database};";
	}

	public function get_model() {
		return [
			"hostname" => $this->hostname,
			"username" => $this->username,
			"password" => $this->password,
			"database" => $this->database,
			"charset"  => $this->charset,
			"port"     => $this->port,
		];
	}

	/**
	 * @return mixed
	 */
	public function get_hostname() {
		return $this->hostname;
	}

	/**
	 * @return mixed
	 */
	public function get_username() {
		return $this->username;
	}

	/**
	 * @return mixed
	 */
	public function get_password() {
		return $this->password;
	}

	/**
	 * @return mixed
	 */
	public function get_database() {
		return $this->database;
	}

	/**
	 * @return mixed
	 */
	public function get_charset() {
		return $this->charset;
	}

	/**
	 * @return mixed
	 */
	public function get_port() {
		return $this->port;
	}
}
?>