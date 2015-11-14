<?php
/*
	НЕ ТРОЖ!!! :)
 */
class X_DB_MySQLi
{
	static $DBC_LIST = [];
	static $LINK = [];
	private $DBLN = false;
	private $DBHOST = "localhost";
	private $DBNAME = "test";
	private $DBUSER = "root";
	private $DBPASS = "";
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
	function __construct()
	{	$cfg = func_get_args();
		if( !(count($cfg)>0) ) throw new Exception("DB_MySQLi_NULL_CONSTRUCT_ARGUMENT", X_Exception_List::DB_MySQLi_NULL_CONSTRUCT_ARGUMENT);
		if(is_array($cfg[0])) {
			if(isset($cfg[0]["DBHOST"])) 	$this->DBHOST = 	$cfg[0]["DBHOST"];
			if(isset($cfg[0]["DBNAME"])) 	$this->DBNAME = 	$cfg[0]["DBNAME"];
			if(isset($cfg[0]["DBUSER"])) 	$this->DBUSER = 	$cfg[0]["DBUSER"];
			if(isset($cfg[0]["DBPASS"])) 	$this->DBPASS = 	$cfg[0]["DBPASS"];
			if(isset($cfg[0]["DBCHARSET"])) $this->DBCHARSET = 	$cfg[0]["DBCHARSET"];
		} else if( count($cfg) == 1 ) {
			if(isset(X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBHOST"])) 	$this->DBHOST = 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBHOST"];
			if(isset(X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBNAME"])) 	$this->DBNAME = 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBNAME"];
			if(isset(X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBUSER"])) 	$this->DBUSER = 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBUSER"];
			if(isset(X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBPASS"])) 	$this->DBPASS = 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBPASS"];
			if(isset(X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBCHARSET"])) $this->DBCHARSET = 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBCHARSET"];
		} else if( count($cfg) == 3 )  
		{ $this->DBNAME = $cfg[0]; $this->DBUSER = $cfg[1]; $this->DBPASS = $cfg[2]; }
		else if( count($cfg) == 4 ) 
		{ $this->DBHOST = $cfg[0]; $this->DBNAME = $cfg[1]; $this->DBUSER = $cfg[2]; $this->DBPASS = $cfg[3]; }		
		else if( count($cfg) == 5 ) 
		{ $this->DBHOST = $cfg[0]; $this->DBNAME = $cfg[1]; $this->DBUSER = $cfg[2]; $this->DBPASS = $cfg[3]; $this->DBCHARSET = $cfg[4]; }
		else throw new Exception("DB_MySQLi_WRONG_CONSTRUCT_ARGUMENT", X_Exception_List::DB_MySQLi_WRONG_CONSTRUCT_ARGUMENT);
		$this->DBLN = md5($this->DBHOST.$this->DBNAME.$this->DBUSER.$this->DBPASS.$this->DBCHARSET);
	}
	// ARGV:
	// 		$DBC, array( "DBHOST" => , "DBNAME" => , "DBUSER" => , "DBPASS" => , "DBCHARSET" => )
	// OR:
	// 		$DBC, $DBNAME, $DBUSER, $DBPASS 			| Defauts: $DBHOST = "localhost"; $DBCHARSET = "utf8";
	// OR:
	// 		$DBC, $DBHOST, $DBNAME, $DBUSER, $DBPASS 	| Defauts: $DBCHARSET = "utf8";
	// OR:
	// 		$DBC, $DBHOST, $DBNAME, $DBUSER, $DBPASS, $DBCHARSET
	static function SetConfig()
	{	$cfg = func_get_args();
		if(isset($cfg[1]) && is_array($cfg[1])) {
			if(isset($cfg[1]["DBHOST"])) 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBHOST"] = 	$cfg[1]["DBHOST"];
			if(isset($cfg[1]["DBNAME"])) 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBNAME"] = 	$cfg[1]["DBNAME"];
			if(isset($cfg[1]["DBUSER"])) 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBUSER"] = 	$cfg[1]["DBUSER"];
			if(isset($cfg[1]["DBPASS"])) 	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBPASS"] = 	$cfg[1]["DBPASS"];
			if(isset($cfg[1]["DBCHARSET"])) X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBCHARSET"] =	$cfg[1]["DBCHARSET"];
		} else if( count($cfg) == 4 ) {	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBNAME"] = $cfg[1]; X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBUSER"] = $cfg[2]; X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBPASS"] = $cfg[3]; 
		} else if( count($cfg) == 5 ) { X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBHOST"] = $cfg[1]; X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBNAME"] = $cfg[2]; 
			X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBUSER"] = $cfg[3]; X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBPASS"] = $cfg[4];
		} else if( count($cfg) == 6 ) { X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBHOST"] = $cfg[1]; X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBNAME"] = $cfg[2];	X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBUSER"] = $cfg[3];
			X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBPASS"] = $cfg[4]; X_DB_MySQLi::$DBC_LIST[$cfg[0]]["DBCHARSET"] = $cfg[5]; }
	}

	public function Connect()
	{
		if(!isset(X_DB_MySQLi::$LINK[$this->DBLN]) || !X_DB_MySQLi::$LINK[$this->DBLN]->ping() )
		{
			X_DB_MySQLi::$LINK[$this->DBLN] = new mysqli( $this->DBHOST, $this->DBUSER, $this->DBPASS, $this->DBNAME );
			X_DB_MySQLi::$LINK[$this->DBLN]->set_charset( $this->DBCHARSET );
			if ( X_DB_MySQLi::$LINK[$this->DBLN]->connect_errno ) 
				throw new Exception(X_DB_MySQLi::$LINK[$this->DBLN]->connect_error, X_DB_MySQLi::$LINK[$this->DBLN]->connect_errno);
		}
		return $this;
	}

	public function add($tableName, $arrayParams, $replace=false) 
	{
		$link_id = $this->Connect()->DBLN;
		$values = array_map( function($string) use ($link_id) { 
			return X_DB_MySQLi::$LINK[$link_id]->real_escape_string($string);
		}, array_values($arrayParams));
		$keys = array_keys($arrayParams);
		if($replace) $type = "REPLACE";
		else $type = "INSERT";
		return $this->insert($type.' INTO `'.$tableName.'` (`'.implode('`,`', $keys).'`) VALUES (\''.implode('\',\'', $values).'\')');
	}

	public function insert($SQL)
	{
		if( X_DB_MySQLi::$LINK[$this->Connect()->DBLN]->real_query($SQL) ) return X_DB_MySQLi::$LINK[$this->DBLN]->insert_id;
		else throw new Exception(X_DB_MySQLi::$LINK[$this->DBLN]->error, X_DB_MySQLi::$LINK[$this->DBLN]->errno);
	}

	public function rq( $SQL )
	{
		if( X_DB_MySQLi::$LINK[$this->Connect()->DBLN]->real_query($SQL) ) return true;
		else throw new Exception(X_DB_MySQLi::$LINK[$this->DBLN]->error, X_DB_MySQLi::$LINK[$this->DBLN]->errno);
	}
	public function get($SQL,$ID_COL=false,$ID_COL2=false,$ID_COL3=false)
	{	
		if($this->rq($SQL))
		{
			$result = X_DB_MySQLi::$LINK[$this->DBLN]->store_result();
			if($result)
			{
				if( $result->num_rows > 0 )
				{
					$DATA = array();
					while ( $row = $result->fetch_assoc() ) 
					{
						if(isset($row[$ID_COL]) && $ID_COL) 
						{
							if(isset($row[$ID_COL2]) && $ID_COL2)
							{
								if(isset($row[$ID_COL3]) && $ID_COL3) $DATA[$row[$ID_COL]][$row[$ID_COL2]][$row[$ID_COL3]] = $row;
								else $DATA[$row[$ID_COL]][$row[$ID_COL2]] = $row;	
							}
							else $DATA[$row[$ID_COL]] = $row;
						}
						else $DATA[] = $row;
					}
					$result->close();
					return $DATA;
				} else {
					$result->close(); 
					return array();
				}
			} else {
				//throw new Exception(X_DB_MySQLi::$LINK[$this->DBLN]->error, X_DB_MySQLi::$LINK[$this->DBLN]->errno);
				return false;
			}
		}
		else return false;
	}

	function __destruct()
	{
		if ( isset( X_DB_MySQLi::$LINK[$this->DBLN] ) && X_DB_MySQLi::$LINK[$this->DBLN]->ping() ) 
			X_DB_MySQLi::$LINK[$this->DBLN]->close();
	}
}
?>