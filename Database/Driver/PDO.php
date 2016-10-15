<?php
/**
 * new PDO(\X\Database\Credetional)
 *
 * 	Functions:
 * 		PDO() - PDO Driver
 * 		query(string $SQL[,int $option = \PDO::FETCH_ASSOC]) - Simple query
 * 		query_num(string $SQL) - alias to query() width $option = \PDO::FETCH_NUM
 * 		insert(string $table, array $data[, bool $replace=false]) - insert Data
 * 		replace(string $table, array $data) - alias to insert width $replace=true
 * 		update(string $table, array $data, array $where) - update row or rows
 *
 *
 * 	Where Examples:
 * 	["name1" => 1, "name2" => "data2"] 					--- WHERE name1 = 1 AND name2 = 'data2'
 * 	["[and]" => ["name1" => 1, "name2" => "data2"]] 	--- WHERE name1 = 1 AND name2 = 'data2'
 * 	["[or]" => ["name1" => 1, "name2" => "data2"]] 		--- WHERE name1 = 1 OR name2 = 'data2'
 * 	["[or]" => ["name||1" => 1, "name||2" => "data2"]] 	--- WHERE name = 1 OR name = 'data2'
 * 	["name" => 1] 					--- WHERE name = 1)
 * 	["name" => [1,2,3,5]] 			--- WHERE name IN ( 1, 2, 3, 5)
 * 	["name|in" => [1,2,3,5]] 		--- WHERE name IN ( 1, 2, 3, 5)
 * 	["name|!" => [1,2,3,5]] 		--- WHERE name NOT IN ( 1, 2, 3, 5)
 * 	["name|!=" => [1,2,3,5]] 		--- WHERE name NOT IN ( 1, 2, 3, 5)
 * 	["name|not" => [1,2,3,5]] 		--- WHERE name NOT IN ( 1, 2, 3, 5)
 * 	["name|not" => 1] 				--- WHERE name != 1)
 * 	["name|<>" => 1] 				--- WHERE name <> 1)
 * 	["name|>" => 1] 				--- WHERE name > 1)
 * 	["name|>=" => 1] 				--- WHERE name >= 1)
 * 	["name|like" => 1] 				--- WHERE name like 1)
 *
 */
namespace X\Database\Driver;
use \X\Database\Credetional as Credetional;
use \X\ETrace\System as ESys;

class PDO_CredetionalError extends ESys {}
class PDO_ConnectionError extends ESys {}
class PDO_UnknownError extends ESys {}
class PDO {
	/**
	 * @var mixed
	 */
	protected $Credetional;
	/**
	 * @var mixed
	 */
	protected $PDO;
	/**
	 * @var int
	 */
	protected $try_count = 0;
	/**
	 * @param $Credetional
	 */
	public function __construct($Credetional) {
		if ($Credetional instanceof Credetional) {
			$this->Credetional = $Credetional;
			$this->connect();
		} else {
			throw new PDO_CredetionalError("Object not valid Credetional", 1, ["Credetional" => $Credetional]);
		}
	}

	public function set_charset() {
		$charset = $this->Credetional->get_charset();
		$this->exec("SET NAMES `{$charset}`");
	}

	protected function connect() {
		try {
			$this->PDO = new \PDO(
				$this->Credetional->get_PDO_MySQL_DSN(),
				$this->Credetional->get_username(),
				$this->Credetional->get_password());
			$this->PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			//$this->PDO->setAttribute(\PDO::ATTR_STATEMENT_CLASS, ['X\Database\Driver\PDOStatement', [$this->PDO]]);
			$this->set_charset();
		} catch (\PDOException $e) {
			throw new PDO_ConnectionError("Connection to database error", 1, [
				"message" => $e->getMessage(),
				"info"    => $e->errorInfo,
			]);
		}
	}

	/**
	 * @return mixed
	 */
	public function PDO() {
		return $this->PDO;
	}

	/**
	 * @param  $SQL
	 * @return mixed
	 */
	public function query($SQL, $option = \PDO::FETCH_ASSOC) {
		try {
			$data = $this->PDO->query($SQL, $option);
			$this->try_count--;
			return $data;
		} catch (\PDOException $e) {
			if ($e->getCode() == "HY000" && $this->try_count < 1) {
				# Механизм восстановления соединения
				$this->connect();
				$this->try_count++;
				return $this->query($SQL, $option);
			} else {
				throw new PDO_UnknownError("PDO Database Error", 1, [
					"SQL"           => $SQL,
					"error_message" => $e->getMessage(),
				]);
			}
		}
	}

	/**
	 * @param  $SQL
	 * @return mixed
	 */
	public function query_num($SQL) {
		# Alias option
		return $this->query($SQL, \PDO::FETCH_NUM);
	}

	/**
	 * @param  $SQL
	 * @return mixed
	 */
	public function exec($SQL) {
		try {
			$data = $this->PDO->exec($SQL);
			$this->try_count--;
			return $data;
		} catch (\PDOException $e) {
			if ($e->getCode() == "HY000" && $this->try_count < 1) {
				# Механизм восстановления соединения
				$this->connect();
				$this->try_count++;
				return $this->exec($SQL);
				# ##################################
			} else {
				throw new PDO_UnknownError("PDO Database Error", 1, [
					"SQL"           => $SQL,
					"error_message" => $e->getMessage(),
				]);
			}
		}
	}

	/**
	 * @param $table
	 * @param $data
	 */
	public function insert($table, $data, $replace = false) {

		$keys    = array_keys($data);
		$pattern = array_map(function ($key) {return ":{$key}";}, $keys);
		if ($replace) {
			$type = "REPLACE";
		} else {
			$type = "INSERT";
		}
		$SQL       = "{$type} INTO `{$table}` (`" . implode('`,`', $keys) . "`) VALUES ( " . implode(", ", $pattern) . " )";
		$statement = $this->prepare($SQL);
		foreach ($keys as $key) {
			$data_bind = $this->check_value($data[$key]);
			if (is_integer($data_bind)) {
				$statement->bindValue(":{$key}", $data_bind, \PDO::PARAM_INT);
			} else {
				$statement->bindValue(":{$key}", $data_bind, \PDO::PARAM_STR);
			}
		}

		if ($statement->execute()) {
			return $this->PDO->lastInsertId();
		}

		return false;
	}

	/**
	 * @param $table
	 * @param $data
	 */
	public function replace($table, $data) {
		$this->insert($table, $data, true);
	}

	/**
	 * @param $SQL
	 */
	public function prepare($SQL) {
		return new PDOStatement($this->PDO, $SQL);
		return $this->PDO->prepare($SQL);
	}

	/**
	 * @param $table
	 * @param $data
	 * @param $where
	 */
	public function update($table, $data, $where = []) {
		$SQL          = "UPDATE `{$table}` SET ";
		$keys         = array_keys($data);
		$data_pattern = array_map(function ($key) {return " `{$key}`= :{$key}";}, $keys);
		$SQL .= implode(", ", $data_pattern);
		$SQL .= " ";
		$whete_obj = new PDOWhereConstructor($where);
		$SQL .= $whete_obj->get_sql();
		$statement = $this->prepare($SQL);
		foreach ($keys as $key) {
			$data_bind = $this->check_value($data[$key]);
			if (is_integer($data_bind)) {
				$statement->bindValue(":{$key}", $data_bind, \PDO::PARAM_INT);
			} else {
				$statement->bindValue(":{$key}", $data_bind, \PDO::PARAM_STR);
			}
		}
		$whete_obj->bind($statement);
		return $statement->execute();
	}

	/**
	 * @param $table
	 * @param $where
	 */
	public function delete($table, $where) {
		$whete_obj = new PDOWhereConstructor($where);
		$SQL       = "DELETE FROM `{$table}` {$whete_obj->get_sql()}";
		$statement = $this->prepare($SQL);
		$whete_obj->bind($statement);
		return $statement->execute();
	}

	/**
	 * @param $table
	 * @param $where
	 * @param $column
	 */
	public function count($table, $where, $column = "*") {
		$whete_obj = new PDOWhereConstructor($where);
		$SQL       = "SELECT count({$column}) FROM `{$table}` {$whete_obj->get_sql()}";
		$statement = $this->prepare($SQL);
		$whete_obj->bind($statement);
		$statement->execute();
		if ($statement->rowCount() > 0) {
			return $statement->fetchAll(\PDO::FETCH_ASSOC)[0]["count({$column})"];
		} else {
			return false;
		}
	}

	/**
	 * @param  $table
	 * @param  array    $where
	 * @param  $order
	 * @param  null     $limit
	 * @param  null     $columns
	 * @return mixed
	 */
	public function simple($table, $where = [], $order = null, $limit = null, $columns = "*") {
		$statement = $this->select_statement($table, $where, $order, 1, $columns);
		if ($statement->rowCount() > 0) {
			return $statement->fetchAll(\PDO::FETCH_ASSOC)[0];
		} else {
			return false;
		}
	}

	/**
	 * @param  $table
	 * @param  array    $where
	 * @param  $order
	 * @param  null     $limit
	 * @param  null     $columns
	 * @return mixed
	 */
	public function select($table, $where = [], $order = null, $limit = null, $columns = "*") {
		return $this->select_statement($table, $where, $order, $limit, $columns)->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * @param $table
	 * @param array    $where
	 * @param $order
	 * @param null     $limit
	 * @param null     $columns
	 */
	public function select_statement($table, $where = [], $order = null, $limit = null, $columns = "*") {
		$this->build_columns($columns);
		$SQL       = "SELECT {$columns} FROM `{$table}` ";
		$whete_obj = new PDOWhereConstructor($where);
		$SQL .= $whete_obj->get_sql();
		if (is_array($order)) {
			if (count($order) == 2) {
				$SQL .= " ORDER BY `{$order[0]}` {$order[1]}";
			} else if (count($order) == 4) {
				$SQL .= " ORDER BY `{$order[0]}` {$order[1]},`{$order[2]}` {$order[3]}";
			}
		}

		if (is_integer($limit)) {
			$SQL .= " LIMIT {$limit}";
		} else if (is_array($limit) && count($limit) == 1) {
			$SQL .= " LIMIT {$limit[0]}";
		} else if (is_array($limit) && count($limit) == 2) {
			$SQL .= " LIMIT {$limit[0]}, {$limit[1]}";
		}
		$statement = $this->prepare($SQL);
		$whete_obj->bind($statement);
		$statement->execute();
		return $statement;
	}

	/**
	 * @param $columns
	 */
	private function build_columns(&$columns) {
		if (is_array($columns) && count($columns) > 0) {
			$columns = "`" . implode("`,`", $columns) . "`";
		} else {
			$columns = "*";
		}
	}

	public function __sleep() {
		return ['Credetional'];
	}

	public function __wakeup() {}

	/**
	 * @param  $value
	 * @return mixed
	 */
	protected function check_value($value) {
		$type = gettype($value);
		switch (strtolower($type)) {
			case 'boolean':
				return $value ? 1 : "0";
				break;

			case 'null':
				return "0";
				break;

			default:
				return $value;
				break;
		}
	}
}

?>