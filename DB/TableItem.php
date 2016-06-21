<?php
namespace X\DB;
class TableItem
{
	protected $table_name;
	protected $sql = "";
	protected $sql_where = "";
	protected $sql_order = "";
	protected $sql_limit = "";
	protected $sql_type;
	protected $driver = null;
	function __construct($name, $driver)
	{
		$this->driver = $driver;
		$this->table_name = $name;
	}

	public function insert($Data)
	{
		$this->sql = "INSERT INTO ".$this->build_insert($Data);
		$this->sql_type = "insert";
		return $this;
	}

	public function replace($Data)
	{
		$this->sql = "REPLACE INTO ".$this->build_insert($Data);
		$this->sql_type = "insert";
		return $this;
	}

	private function build_insert($Data)
	{
		$keys = "`".implode('`,`', array_keys($Data))."`";
		$_values = array_values($Data);
		for ($i=0; $i < count($_values); $i++) 
		{
			$_values[$i] = $this->escape($_values[$i]);
		}
		$values = "'".implode("','", $_values)."'";
		return "`{$this->table_name}` ({$keys}) VALUES ({$values})";
	}

	public function update($Data)
	{
		if( !is_array($Data) || count($Data) == 0 ) return false;
		$params = "";
		foreach ($Data as $key => $value) {
			$params .= strlen($params)==0?"":", ";
			$params .= "`".$key."` = '".$this->escape($value)."'";
		}
		$this->sql = "UPDATE `{$this->table_name}` SET {$params}";
		$this->sql_type = "update";
		return $this;
	}

	public function select($columns = [])
	{
		if(count($columns)>0)
			$columns = "`".implode("`,`", $columns)."`";
		else $columns = "*";
		$this->sql_type = "select";
		$this->sql = "SELECT {$columns} FROM `{$this->table_name}`";
		return $this;
	}

	public function where($data)
	{
		$this->sql_where = " WHERE " . $this->where_clause($data);
		return $this;
	}

	private function where_clause($data, $operator = false, $gr=false)
	{
		$where = "";
		if(is_array($data))
		{
			if($operator == false && count($data)==2)
			{
				$where .= $this->where_operation($data[0], $data[1])." ";
			}
			else
			{
				$operations = [];
				foreach ($data as $key => $value) 
				{
					if( strtolower($key) == "and" || strtolower($key) == "or" ) 
						$operations[] = (($gr)?"(":"").
										$this->where_clause($value, strtolower($key),true).
										(($gr)?") ":" ");
					else $operations[] = $this->where_operation($key, $value);
				}
				$where .= implode(" ".strtoupper($operator)." ", $operations);
			}
			return $where;
		}
		if(is_string($data)) return $data;
	}

	private function array_quote($array)
	{
		$temp = array();

		foreach ($array as $value)
		{
			$temp[] = is_int($value) ? $value : $this->escape($value);
		}

		return implode($temp, ',');
	}

	private function where_operation($key, $value)
	{
		$wheres = [];
		$type = gettype($value);
		preg_match('/(#?)([\w\.\-]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);

		$column = "`".$match[ 2 ]."`";

		if (isset($match[ 4 ]))
		{
			$operator = $match[ 4 ];

			if ($operator == '!')
			{
				switch ($type)
				{
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
						$wheres[] = $column . ' != ' . "'".$this->escape($value)."'";
						break;
				}
			}

			if ($operator == '<>' || $operator == '><')
			{
				if ($type == 'array')
				{
					if ($operator == '><')
					{
						$column .= ' NOT';
					}

					if (is_numeric($value[ 0 ]) && is_numeric($value[ 1 ]))
					{
						$wheres[] = '(' . $column . ' BETWEEN ' . $value[ 0 ] . ' AND ' . $value[ 1 ] . ')';
					}
					else
					{
						$wheres[] = '(' . $column . ' BETWEEN ' . "'".$this->escape($value[ 0 ])."'" . ' AND ' . "'".$this->escape($value[ 1 ])."'" . ')';
					}
				}
			}

			if ($operator == '~' || $operator == '!~')
			{
				if ($type != 'array')
				{
					$value = array($value);
				}

				$like_clauses = array();

				foreach ($value as $item)
				{
					$item = strval($item);
					$suffix = mb_substr($item, -1, 1);

					if ($suffix === '_')
					{
						$item = substr_replace($item, '%', -1);
					}
					elseif ($suffix === '%')
					{
						$item = '%' . substr_replace($item, '', -1, 1);
					}
					elseif (preg_match('/^(?!%).+(?<!%)$/', $item))
					{
						$item = '%' . $item . '%';
					}

					$like_clauses[] = $column . ($operator === '!~' ? ' NOT' : '') . ' LIKE ' . "'".$this->escape($item)."'";
				}

				$wheres[] = implode(' OR ', $like_clauses);
			}

			if (in_array($operator, array('>', '>=', '<', '<=')))
			{
				if (is_numeric($value))
				{
					$wheres[] = $column . ' ' . $operator . ' ' . $value;
				}
				else
				{
					$wheres[] = $column . ' ' . $operator . ' ' . "'".$this->escape($value)."'";
				}
			}
		}
		else
		{
			switch ($type)
			{
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
					$wheres[] = $column . ' = ' . "'".$this->escape($value)."'";
					break;
			}
		}	
		return implode(' ', $wheres);	
	}

	public function order($coll, $type)
	{
		$this->sql_order = " OREDR BY `{$coll}` {$type}";
		return $this;
	}

	public function limit( $op1, $op2 = false );
	{
		if($op2) $this->sql_limit = " LIMIT {$op1},{$op2}";
		else $this->sql_limit = " LIMIT {$op1}";
		return $this;
	}
	private function escape($data)
	{
		if($this->driver instanceof \X_DB_MySQLi) return $this->driver->esc($data);
	}

	public function getSQL()
	{
		return $this->sql.$this->sql_where;
	}

	public function exec($op1=false,$op2=false,$op3=false)
	{
		if($this->sql_type == "update" && $this->driver instanceof \X_DB_MySQLi) 
			return $this->driver->rq($this->sql . $this->sql_where);
		if($this->sql_type == "insert" && $this->driver instanceof \X_DB_MySQLi) 
			return $this->driver->insert( $this->sql );
		if($this->sql_type == "select" && $this->driver instanceof \X_DB_MySQLi) 
			return $this->driver->get($this->sql . $this->sql_where  . $this->sql_order . $this->sql_limit ,$op1,$op2,$op3);
		else throw new \Exception("Internal error", 0);
	}
}
?>