<?php
/**
* 
*/
class X_ErrorWriter
{
	static $AllErrorList = [];
	public $ErrorList = [];
	function __construct($Type, $Message, $File, $Line, $StackTrace=false)
	{
		$this->fixError($Type, $Message, $File, $Line, $StackTrace);
	}

	public function fixError($Type, $Message, $File, $Line, $StackTrace=false)
	{
		$File = str_replace(ROOT_DIR, "", $File);
		$Message = str_replace(ROOT_DIR, "", $Message);
		$StackTrace = str_replace(ROOT_DIR, "", $StackTrace);

		$ErrorIndex = md5($Type.$Message.$File.$Line);

		if(isset($this->ErrorList[$ErrorIndex])) $this->ErrorList[$ErrorIndex]['count']++;
		else
		{
			$this->ErrorList[$ErrorIndex]['id'] = $ErrorIndex;
			$this->ErrorList[$ErrorIndex]['count'] = 1;
			$this->ErrorList[$ErrorIndex]['number'] = $Type;
			$this->ErrorList[$ErrorIndex]['message'] = $StackTrace?$Message."\n".$StackTrace:$Message;
			$this->ErrorList[$ErrorIndex]['file'] = $File;
			$this->ErrorList[$ErrorIndex]['line'] = $Line;
			$this->ErrorList[$ErrorIndex]['time'] = time();
		}
		X_ErrorWriter::$AllErrorList[$ErrorIndex] = $this->ErrorList[$ErrorIndex];
		return $this;
	}

	public function tryWriteErrorToDB()
	{
		if(count($this->ErrorList)>0)
		{	
			$DB = ( new X_DB_MySQLi("sw") )->Connect();
			$lastErrors = $DB->get("SELECT * from `error_log` where `id` in ('".implode( "','", array_keys($this->ErrorList) )."') order by `time` desc","id");
			if(count($lastErrors)>0)
			{
				foreach ($lastErrors as $key => $value) 
				{
					$this->ErrorList[$key]['count'] = $this->ErrorList[$key]['count'] + $value['count'];
				}
			}
			foreach ($this->ErrorList as $key => $data) 
			{
				if( $DB->add("error_log", $data, true) )
					unset($this->ErrorList[$key]);
			}
		}		
	}

	function __destruct(){}
}
?>