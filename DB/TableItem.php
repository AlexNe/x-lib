<?php
namespace X\DB;
class TableItem {
	/**
	 * @var mixed
	 */
	protected $table_name;
	/**
	 * @var mixed
	 */
	protected $data_param;
	/**
	 * @var mixed
	 */
	protected $data_where;
	/**
	 * @var mixed
	 */
	protected $data_columns;
	/**
	 * @var mixed
	 */
	protected $data_order;
	/**
	 * @var mixed
	 */
	protected $data_limit;
	/**
	 * @var string
	 */
	protected $sql = "";
	/**
	 * @var string
	 */
	protected $sql_where = "";
	/**
	 * @var string
	 */
	protected $sql_order = "";
	/**
	 * @var string
	 */
	protected $sql_limit = "";
	/**
	 * @var mixed
	 */
	protected $sql_type;
	/**
	 * @var mixed
	 */
	protected $driver = null;
	/**
	 * @param $name
	 * @param $driver
	 */
	public function __construct($name, $driver) {
		$this->driver     = $driver;
		$this->table_name = $name;
	}

	/**
	 * @param  $Data
	 * @return mixed
	 */
	public function insert($Data) {
		$this->sql_type = "insert";
		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_param = $Data;
			return $this;
		}
		$this->sql = "INSERT INTO " . $this->build_insert($Data);
		return $this;
	}

	/**
	 * @param  $Data
	 * @return mixed
	 */
	public function replace($Data) {
		$this->sql_type = "replace";
		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_param = $Data;
			return $this;
		}
		$this->sql = "REPLACE INTO " . $this->build_insert($Data);
		return $this;
	}

	/**
	 * @param $Data
	 */
	private function build_insert($Data) {
		$keys    = "`" . implode('`,`', array_keys($Data)) . "`";
		$_values = array_values($Data);
		for ($i = 0; $i < count($_values); $i++) {
			$_values[$i] = $this->escape($_values[$i]);
		}
		$values = "'" . implode("','", $_values) . "'";
		return "`{$this->table_name}` ({$keys}) VALUES ({$values})";
	}

	/**
	 * @param $Data
	 */
	public function update($Data) {
		$this->sql_type = "update";
		if ( ! is_array($Data) || count($Data) == 0) {
			return false;
		}
		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_param = $Data;
			return $this;
		}

		$params = "";
		foreach ($Data as $key => $value) {
			$params .= strlen($params) == 0 ? "" : ", ";
			$params .= "`" . $key . "` = '" . $this->escape($value) . "'";
		}
		$this->sql = "UPDATE `{$this->table_name}` SET {$params}";
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function first() {
		$this->sql_type = "first";
		$columns        = func_get_args();
		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_columns = $columns;
			return $this;
		}

		if (count($columns) == 1 && is_array($columns[0])) {
			$columns = "`" . implode("`,`", $columns[0]) . "`";
		} else if (count($columns) > 1) {
			$columns = "`" . implode("`,`", $columns) . "`";
		} else {
			$columns = "*";
		}

		$this->sql = "SELECT {$columns} FROM `{$this->table_name}`";
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function select() {
		$this->sql_type = "select";
		$columns        = func_get_args();

		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_columns = $columns;
			return $this;
		}

		if (count($columns) == 1 && is_array($columns[0])) {
			$columns = "`" . implode("`,`", $columns[0]) . "`";
		} else if (count($columns) > 1) {
			$columns = "`" . implode("`,`", $columns) . "`";
		} else {
			$columns = "*";
		}

		$this->sql = "SELECT {$columns} FROM `{$this->table_name}`";
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function delete() {
		$this->sql_type = "delete";

		$this->sql = "DELETE FROM `{$this->table_name}`";
		return $this;
	}

	/**
	 * @param  $data
	 * @return mixed
	 */
	public function where($data) {
		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_where = $data;
			return $this;
		}
		$this->sql_where = " WHERE " . $this->where_clause($data);
		return $this;
	}

	/**
	 * @param  $data
	 * @param  $operator
	 * @param  false       $gr
	 * @return mixed
	 */
	private function where_clause($data, $operator = false, $gr = false) {
		$where = "";
		if (is_array($data)) {
			if ($operator == false && count($data) == 2) {
				$where .= $this->where_operation($data[0], $data[1]) . " ";
			} else {
				$operations = [];
				foreach ($data as $key => $value) {
					if (strtolower($key) == "and" || strtolower($key) == "or") {
						$operations[] = (($gr) ? "(" : "") .
						$this->where_clause($value, strtolower($key), true) .
							(($gr) ? ") " : " ");
					} else {
						$operations[] = $this->where_operation($key, $value);
					}
				}
				$where .= implode(" " . strtoupper($operator) . " ", $operations);
			}
			return $where;
		}
		if (is_string($data)) {
			return $data;
		}
	}

	/**
	 * @param $array
	 */
	private function array_quote($array) {
		$temp = [];

		foreach ($array as $value) {
			$temp[] = is_int($value) ? $value : $this->escape($value);
		}

		return implode($temp, ',');
	}

	/**
	 * @param $key
	 * @param $value
	 */
	private function where_operation($key, $value) {
		$wheres = [];
		$type   = gettype($value);
		preg_match('/(#?)([\w\.\-]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);

		$column = "`" . $match[2] . "`";

		if (isset($match[4])) {
			$operator = $match[4];

			if ($operator == '!') {
				switch ($type) {
					case 'NULL':
						$wheres[] = $column . ' IS NOT NULL';
						break;

					case 'array':
						$wheres[] = $column . ' NOT IN (' . $this->array_quote($value) . ')';
						break;

					case 'integer':
					case 'double':
						$wheres[] = $column . ' != ' . $value;
						break;

					case 'boolean':
						$wheres[] = $column . ' != ' . ($value ? '1' : '0');
						break;

					case 'string':
						$wheres[] = $column . ' != ' . "'" . $this->escape($value) . "'";
						break;
				}
			}

			if ($operator == '<>' || $operator == '><') {
				if ($type == 'array') {
					if ($operator == '><') {
						$column .= ' NOT';
					}

					if (is_numeric($value[0]) && is_numeric($value[1])) {
						$wheres[] = '(' . $column . ' BETWEEN ' . $value[0] . ' AND ' . $value[1] . ')';
					} else {
						$wheres[] = '(' . $column . ' BETWEEN ' . "'" . $this->escape($value[0]) . "'" . ' AND ' . "'" . $this->escape($value[1]) . "'" . ')';
					}
				}
			}

			if ($operator == '~' || $operator == '!~') {
				if ($type != 'array') {
					$value = [$value];
				}

				$like_clauses = [];

				foreach ($value as $item) {
					$item   = strval($item);
					$suffix = mb_substr($item, -1, 1);

					if ($suffix === '_') {
						$item = substr_replace($item, '%', -1);
					} else if ($suffix === '%') {
						$item = '%' . substr_replace($item, '', -1, 1);
					} else if (preg_match('/^(?!%).+(?<!%)$/', $item)) {
						$item = '%' . $item . '%';
					}

					$like_clauses[] = $column . ($operator === '!~' ? ' NOT' : '') . ' LIKE ' . "'" . $this->escape($item) . "'";
				}

				$wheres[] = implode(' OR ', $like_clauses);
			}

			if (in_array($operator, ['>', '>=', '<', '<='])) {
				if (is_numeric($value)) {
					$wheres[] = $column . ' ' . $operator . ' ' . $value;
				} else {
					$wheres[] = $column . ' ' . $operator . ' ' . "'" . $this->escape($value) . "'";
				}
			}
		} else {
			switch ($type) {
				case 'NULL':
					$wheres[] = $column . ' IS NULL';
					break;

				case 'array':
					$wheres[] = $column . ' IN (' . $this->array_quote($value) . ')';
					break;

				case 'integer':
				case 'double':
					$wheres[] = $column . ' = ' . $value;
					break;

				case 'boolean':
					$wheres[] = $column . ' = ' . ($value ? '1' : '0');
					break;

				case 'string':
					$wheres[] = $column . ' = ' . "'" . $this->escape($value) . "'";
					break;
			}
		}
		return implode(' ', $wheres);
	}

	/**
	 * @param $coll
	 * @param $type
	 */
	public function order($coll, $type) {
		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_order = [$coll, $type];
			return $this;
		}
		$this->sql_order = "  ORDER BY `{$coll}` {$type}";
		return $this;
	}

	/**
	 * @param $op1
	 * @param $op2
	 */
	public function limit($op1, $op2 = false) {
		if ($this->driver instanceof \X\Database\Driver\PDO) {
			$this->data_limit = [$op1];
			if ($op2) {$this->data_limit[] = $op2;}
			return $this;
		}
		if ($op2) {
			$this->sql_limit = " LIMIT {$op1},{$op2}";
		} else {
			$this->sql_limit = " LIMIT {$op1}";
		}

		return $this;
	}

	/**
	 * @param  $data
	 * @return mixed
	 */
	private function escape($data) {
		if ($this->driver instanceof \X_DB_MySQLi) {
			return $this->driver->esc($data);
		}
		return $data;
	}

	/**
	 * @return mixed
	 */
	public function getSQL() {
		return $this->sql . $this->sql_where;
	}

	/**
	 * @param  $op1
	 * @param  false   $op2
	 * @param  false   $op3
	 * @return mixed
	 */
	public function exec($op1 = false, $op2 = false, $op3 = false) {
		if ($this->sql_type == "update" && $this->driver instanceof \X_DB_MySQLi) {
			return $this->driver->rq($this->sql . $this->sql_where);
		}
		if ($this->sql_type == "insert" && $this->driver instanceof \X_DB_MySQLi) {
			return $this->driver->insert($this->sql);
		}
		if ($this->sql_type == "select" && $this->driver instanceof \X_DB_MySQLi) {
			return $this->driver->get($this->sql . $this->sql_where . $this->sql_order . $this->sql_limit, $op1, $op2, $op3);
		}
		if ($this->sql_type == "first" && $this->driver instanceof \X_DB_MySQLi) {
			return $this->driver->first($this->sql . $this->sql_where . $this->sql_order . $this->sql_limit, $op1, $op2, $op3);
		}
		if ($this->sql_type == "delete" && $this->driver instanceof \X_DB_MySQLi) {
			return $this->driver->rq($this->sql . $this->sql_where);
		}

		if ($this->sql_type == "update" && $this->driver instanceof \X\Database\Driver\PDO) {
			return $this->driver->update($this->table_name, $this->data_param, $this->data_where);
		}
		if ($this->sql_type == "insert" && $this->driver instanceof \X\Database\Driver\PDO) {
			return $this->driver->insert($this->table_name, $this->data_param);
		}
		if ($this->sql_type == "replace" && $this->driver instanceof \X\Database\Driver\PDO) {
			return $this->driver->replace($this->table_name, $this->data_param);
		}
		if ($this->sql_type == "select" && $this->driver instanceof \X\Database\Driver\PDO) {
			return $this->driver->select(
				$this->table_name,
				$this->data_where,
				$this->data_order,
				$this->data_limit,
				$this->data_columns);
		}
		if ($this->sql_type == "first" && $this->driver instanceof \X\Database\Driver\PDO) {
			return $this->driver->simple(
				$this->table_name,
				$this->data_where,
				$this->data_order,
				$this->data_limit,
				$this->data_columns);
		}
		if ($this->sql_type == "delete" && $this->driver instanceof \X\Database\Driver\PDO) {
			$whete_obj = new \X\Database\Driver\PDOWhereConstructor($this->data_where);
			return $this->driver->exec($this->sql . $whete_obj->get_sql());
		}

		throw new \Exception("Internal error", 0);
	}
}
?>