<?php
namespace X\DB;
class TableItem {
	/**
	 * @var mixed
	 */
	protected $table_name;
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
		$this->sql      = "INSERT INTO " . $this->build_insert($Data);
		$this->sql_type = "insert";
		return $this;
	}

	/**
	 * @param  $Data
	 * @return mixed
	 */
	public function replace($Data) {
		$this->sql      = "REPLACE INTO " . $this->build_insert($Data);
		$this->sql_type = "insert";
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
		if ( ! is_array($Data) || count($Data) == 0) {
			return false;
		}

		$params = "";
		foreach ($Data as $key => $value) {
			$params .= strlen($params) == 0 ? "" : ", ";
			$params .= "`" . $key . "` = '" . $this->escape($value) . "'";
		}
		$this->sql      = "UPDATE `{$this->table_name}` SET {$params}";
		$this->sql_type = "update";
		return $this;
	}

	public function select() {
		$columns = func_get_args();
		if (count($columns) == 1 && is_array($columns[0])) {
			$columns = "`" . implode("`,`", $columns[0]) . "`";
		} else if (count($columns) > 1) {
			$columns = "`" . implode("`,`", $columns) . "`";
		} else {
			$columns = "*";
		}

		$this->sql_type = "select";
		$this->sql      = "SELECT {$columns} FROM `{$this->table_name}`";
		return $this;
	}

	public function delete() {
		$this->sql_type = "delete";
		$this->sql      = "DELETE FROM `{$this->table_name}`";
		return $this;
	}

	/**
	 * @param  $data
	 * @return mixed
	 */
	public function where($data) {
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
		$this->sql_order = "  ORDER BY `{$coll}` {$type}";
		return $this;
	}

	/**
	 * @param $op1
	 * @param $op2
	 */
	public function limit($op1, $op2 = false) {
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
		if ($this->sql_type == "delete" && $this->driver instanceof \X_DB_MySQLi) {
			return $this->driver->rq($this->sql . $this->sql_where);
		}

		throw new \Exception("Internal error", 0);
	}
}
?>