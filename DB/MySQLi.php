<?php
/*
НЕ ТРОЖ!!! :)
 */
class X_DB_MySQLi
{
	/**
	 * @var array
	 */
	static $DBC_LIST = [];
	/**
	 * @var array
	 */
	static $LINK = [];
	/**
	 * @var mixed
	 */
	private $DBLN = false;
	/**
	 * @var string
	 */
	private $DBHOST = "localhost";
	/**
	 * @var string
	 */
	private $DBNAME = "test";
	/**
	 * @var string
	 */
	private $DBUSER = "root";
	/**
	 * @var string
	 */
	private $DBPASS = "";
	/**
	 * @var string
	 */
	private $DBCHARSET = "utf8";
	// ARGV:
	// 		array( "DBHOST" => , "DBNAME" => , "DBUSER" => , "DBPASS" => , "DBCHARSET" => )
	// OR:
	// 		$DBNAME, $DBUSER, $DBPASS 			| Defauts: $DBHOST = "localhost"; $DBCHARSET = "utf8";
	// OR:
	// 		$DBHOST, $DBNAME, $DBUSER, $DBPASS 	| Defauts: $DBCHARSET = "utf8";
	// OR:
	// 		$DBHOST, $DBNAME, $DBUSER, $DBPASS, $DBCHARSET
	// OR:
	// 		$DBC - name of configured database
	public function __construct()
	{
		$cfg = func_get_args();
		if ( ! (count($cfg) > 0))
		{
			throw new Exception("DB_MySQLi_NULL_CONSTRUCT_ARGUMENT", X_Exception_List::DB_MySQLi_NULL_CONSTRUCT_ARGUMENT);
		}

		if (is_array($cfg[0]))
		{
			if (isset($cfg[0]["DBHOST"]))
			{
				$this->DBHOST = $cfg[0]["DBHOST"];
			}

			if (isset($cfg[0]["DBNAME"]))
			{
				$this->DBNAME = $cfg[0]["DBNAME"];
			}

			if (isset($cfg[0]["DBUSER"]))
			{
				$this->DBUSER = $cfg[0]["DBUSER"];
			}

			if (isset($cfg[0]["DBPASS"]))
			{
				$this->DBPASS = $cfg[0]["DBPASS"];
			}

			if (isset($cfg[0]["DBCHARSET"]))
			{
				$this->DBCHARSET = $cfg[0]["DBCHARSET"];
			}
		}
		else if (count($cfg) == 1)
		{
			if (isset(self::$DBC_LIST[$cfg[0]]["DBHOST"]))
			{
				$this->DBHOST = self::$DBC_LIST[$cfg[0]]["DBHOST"];
			}

			if (isset(self::$DBC_LIST[$cfg[0]]["DBNAME"]))
			{
				$this->DBNAME = self::$DBC_LIST[$cfg[0]]["DBNAME"];
			}

			if (isset(self::$DBC_LIST[$cfg[0]]["DBUSER"]))
			{
				$this->DBUSER = self::$DBC_LIST[$cfg[0]]["DBUSER"];
			}

			if (isset(self::$DBC_LIST[$cfg[0]]["DBPASS"]))
			{
				$this->DBPASS = self::$DBC_LIST[$cfg[0]]["DBPASS"];
			}

			if (isset(self::$DBC_LIST[$cfg[0]]["DBCHARSET"]))
			{
				$this->DBCHARSET = self::$DBC_LIST[$cfg[0]]["DBCHARSET"];
			}
		}
		else if (count($cfg) == 3)
		{
			$this->DBNAME = $cfg[0];
			$this->DBUSER = $cfg[1];
			$this->DBPASS = $cfg[2];}
		else if (count($cfg) == 4)
		{
			$this->DBHOST = $cfg[0];
			$this->DBNAME = $cfg[1];
			$this->DBUSER = $cfg[2];
			$this->DBPASS = $cfg[3];}
		else if (count($cfg) == 5)
		{
			$this->DBHOST    = $cfg[0];
			$this->DBNAME    = $cfg[1];
			$this->DBUSER    = $cfg[2];
			$this->DBPASS    = $cfg[3];
			$this->DBCHARSET = $cfg[4];}
		else
		{
			throw new Exception("DB_MySQLi_WRONG_CONSTRUCT_ARGUMENT", X_Exception_List::DB_MySQLi_WRONG_CONSTRUCT_ARGUMENT);
		}

		$this->DBLN = md5($this->DBHOST . $this->DBNAME . $this->DBUSER . $this->DBPASS . $this->DBCHARSET);
	}

	// ARGV:
	// 		$DBC, array( "DBHOST" => , "DBNAME" => , "DBUSER" => , "DBPASS" => , "DBCHARSET" => )
	// OR:
	// 		$DBC, $DBNAME, $DBUSER, $DBPASS 			| Defauts: $DBHOST = "localhost"; $DBCHARSET = "utf8";
	// OR:
	// 		$DBC, $DBHOST, $DBNAME, $DBUSER, $DBPASS 	| Defauts: $DBCHARSET = "utf8";
	// OR:
	// 		$DBC, $DBHOST, $DBNAME, $DBUSER, $DBPASS, $DBCHARSET
	public static function SetConfig()
	{
		$cfg = func_get_args();
		if (isset($cfg[1]) && is_array($cfg[1]))
		{
			if (isset($cfg[1]["DBHOST"]))
			{
				self::$DBC_LIST[$cfg[0]]["DBHOST"] = $cfg[1]["DBHOST"];
			}

			if (isset($cfg[1]["DBNAME"]))
			{
				self::$DBC_LIST[$cfg[0]]["DBNAME"] = $cfg[1]["DBNAME"];
			}

			if (isset($cfg[1]["DBUSER"]))
			{
				self::$DBC_LIST[$cfg[0]]["DBUSER"] = $cfg[1]["DBUSER"];
			}

			if (isset($cfg[1]["DBPASS"]))
			{
				self::$DBC_LIST[$cfg[0]]["DBPASS"] = $cfg[1]["DBPASS"];
			}

			if (isset($cfg[1]["DBCHARSET"]))
			{
				self::$DBC_LIST[$cfg[0]]["DBCHARSET"] = $cfg[1]["DBCHARSET"];
			}
		}
		else if (count($cfg) == 4)
		{
			self::$DBC_LIST[$cfg[0]]["DBNAME"] = $cfg[1];
			self::$DBC_LIST[$cfg[0]]["DBUSER"] = $cfg[2];
			self::$DBC_LIST[$cfg[0]]["DBPASS"] = $cfg[3];
		}
		else if (count($cfg) == 5)
		{
			self::$DBC_LIST[$cfg[0]]["DBHOST"] = $cfg[1];
			self::$DBC_LIST[$cfg[0]]["DBNAME"] = $cfg[2];
			self::$DBC_LIST[$cfg[0]]["DBUSER"] = $cfg[3];
			self::$DBC_LIST[$cfg[0]]["DBPASS"] = $cfg[4];
		}
		else if (count($cfg) == 6)
		{
			self::$DBC_LIST[$cfg[0]]["DBHOST"]    = $cfg[1];
			self::$DBC_LIST[$cfg[0]]["DBNAME"]    = $cfg[2];
			self::$DBC_LIST[$cfg[0]]["DBUSER"]    = $cfg[3];
			self::$DBC_LIST[$cfg[0]]["DBPASS"]    = $cfg[4];
			self::$DBC_LIST[$cfg[0]]["DBCHARSET"] = $cfg[5];}
	}

	/**
	 * @return mixed
	 */
	public function Connect()
	{
		if ( ! isset(self::$LINK[$this->DBLN])
			|| ! (self::$LINK[$this->DBLN] instanceof mysqli)
			|| ! self::$LINK[$this->DBLN]->ping())
		{
			self::$LINK[$this->DBLN] = new mysqli($this->DBHOST, $this->DBUSER, $this->DBPASS, $this->DBNAME);
			self::$LINK[$this->DBLN]->set_charset($this->DBCHARSET);
			if (self::$LINK[$this->DBLN]->connect_errno)
			{
				throw new Exception(self::$LINK[$this->DBLN]->connect_error, self::$LINK[$this->DBLN]->connect_errno);
			}
		}
		return $this;
	}

	/**
	 * @param  $tableName
	 * @param  $arrayParams
	 * @param  $replace
	 * @return mixed
	 */
	public function add($tableName, $arrayParams, $replace = false)
	{
		$link_id = $this->Connect()->DBLN;
		$values  = array_map(function ($string) use ($link_id)
		{
			return X_DB_MySQLi::$LINK[$link_id]->real_escape_string($string);
		}, array_values($arrayParams));
		$keys = array_keys($arrayParams);
		if ($replace)
		{
			$type = "REPLACE";
		}
		else
		{
			$type = "INSERT";
		}

		return $this->insert($type . ' INTO `' . $tableName . '` (`' . implode('`,`', $keys) . '`) VALUES (\'' . implode('\',\'', $values) . '\')');
	}

	/**
	 * @param  $tableName
	 * @param  $arrayParams
	 * @param  $whereParams
	 * @return mixed
	 */
	public function update($tableName, $arrayParams, $whereParams = null)
	{
		if ( ! is_array($arrayParams) || count($arrayParams) == 0)
		{
			return false;
		}

		$params = "";
		foreach ($arrayParams as $key => $value)
		{
			$params .= "`" . $key . "` = '" . $this->esc($value) . "',";
		}
		$params = trim($params, ",");
		$WHERE  = "";
		if ($whereParams != null)
		{
			if (is_array($whereParams) && count($whereParams) > 0 && is_string($whereParams[0]))
			{
				$WHERE = "WHERE " . $this->construct_where_param($whereParams);
			}
			else if (is_array($whereParams) && count($whereParams) > 0 && is_array($whereParams[0]) && count($whereParams[0]) > 0)
			{
				foreach ($whereParams as $value)
				{
					if (strlen($WHERE) == 0)
					{
						$WHERE = "WHERE ";
					}
					else
					{
						$WHERE .= " AND ";
					}

					$WHERE .= $this->construct_where_param($value);
				}
			}
		}
		return $this->rq('UPDATE `' . $tableName . '` SET ' . $params . ' ' . $WHERE);
	}

	/**
	 * @param $whereParams
	 */
	private function construct_where_param($whereParams)
	{
		if (count($whereParams) == 2)
		{
			return "`" . $whereParams[0] . "`='" . $this->esc($whereParams[1]) . "'";
		}

		if (count($whereParams) == 3)
		{
			return "`" . $whereParams[0] . "`" . $whereParams[1] . "'" . $this->esc($whereParams[2]) . "'";
		}
	}

	/**
	 * @param $SQL
	 */
	public function insert($SQL)
	{
		if (self::$LINK[$this->Connect()->DBLN]->real_query($SQL))
		{
			return self::$LINK[$this->DBLN]->insert_id;
		}
		else
		{
			throw new Exception(self::$LINK[$this->DBLN]->error, self::$LINK[$this->DBLN]->errno);
		}
	}

	/**
	 * @param $value
	 */
	public function esc($value)
	{
		return self::$LINK[$this->Connect()->DBLN]->real_escape_string($value);
	}

	/**
	 * Real Query
	 * @param $SQL
	 */
	public function rq($SQL)
	{
		if (self::$LINK[$this->Connect()->DBLN]->real_query($SQL))
		{
			return true;
		}
		else
		{
			throw new Exception(self::$LINK[$this->DBLN]->error, self::$LINK[$this->DBLN]->errno);
		}
	}

	/**
	 * Simple Query (Sample: For "Call Procedure()")
	 * @param $SQL
	 */
	public function sq($SQL)
	{
		if (self::$LINK[$this->Connect()->DBLN]->query($SQL))
		{
			return true;
		}
		else
		{
			throw new Exception(self::$LINK[$this->DBLN]->error, self::$LINK[$this->DBLN]->errno);
		}
	}

	/**
	 * Analod SQL SELECT
	 * @param  $SQL
	 * @param  $ID_COL
	 * @param  false     $ID_COL2
	 * @param  false     $ID_COL3
	 * @return Array     or false
	 */
	public function get($SQL, $ID_COL = false, $ID_COL2 = false, $ID_COL3 = false)
	{
		if ($this->rq($SQL))
		{
			$result = self::$LINK[$this->DBLN]->store_result();
			if ($result)
			{
				if ($result->num_rows > 0)
				{
					$DATA = [];
					while ($row = $result->fetch_assoc())
					{
						if (isset($row[$ID_COL]) && $ID_COL)
						{
							if (isset($row[$ID_COL2]) && $ID_COL2)
							{
								if (isset($row[$ID_COL3]) && $ID_COL3)
								{
									$DATA[$row[$ID_COL]][$row[$ID_COL2]][$row[$ID_COL3]] = $row;
								}
								else
								{
									$DATA[$row[$ID_COL]][$row[$ID_COL2]] = $row;
								}
							}
							else
							{
								$DATA[$row[$ID_COL]] = $row;
							}
						}
						else
						{
							$DATA[] = $row;
						}
					}
					$result->close();
					return $DATA;
				}
				else
				{
					$result->close();
					return [];
				}
			}
			else
			{
				//throw new Exception(X_DB_MySQLi::$LINK[$this->DBLN]->error, X_DB_MySQLi::$LINK[$this->DBLN]->errno);
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * @param  $table
	 * @param  $column
	 * @param  $data
	 * @return mixed
	 */
	public function delete_in($table, $column, $data)
	{
		if ( ! (count($data) > 0))
		{
			return false;
		}

		$SQL = "DELETE FROM";
		$SQL .= " `" . $table . "` ";
		$SQL .= "WHERE";
		$SQL .= " `" . $column . "` in('";
		$SQL .= implode("','", $data);
		$SQL .= "')";
		return $this->rq($SQL);
	}

	public function __destruct()
	{
		if (isset(self::$LINK[$this->DBLN])
			&& self::$LINK[$this->DBLN] instanceof mysqli
			&& self::$LINK[$this->DBLN]->ping()
		)
		{
			self::$LINK[$this->DBLN]->close();
			unset(self::$LINK[$this->DBLN]);
		}
	}
}
?>