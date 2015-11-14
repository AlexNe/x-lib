<?php
class X_Proc_Stats
{
	private $CPU_COUNT = 1;
	private $CMD = "/bin/ps -auxw";
	private $DATABASE = array();
	private $RAW;
	private $RAW_LINES;

	function __construct($CPU_COUNT)
	{
		$this->CPU_COUNT = $CPU_COUNT;
		$this->RAW = `$this->CMD`;
		$this->RAW_LINES = explode("\n", $this->RAW);

		foreach ($this->RAW_LINES as $key => $value) 
		{
			$line = str_replace("  ", " ", $value);
			$line = str_replace("  ", " ", $line);
			$line = str_replace("  ", " ", $line);
			$line = str_replace("  ", " ", $line);
			$line = str_replace("  ", " ", $line);
			$line = str_replace("  ", " ", $line);
			$cols["data"] = explode(" ", $line);
			$cols["raw"] = $line;
			$this->DATABASE[] = $cols;
		}
	}

	public function get_pcpu()
	{
		$programs = func_get_args();
		$pcpu = 0;
		foreach ($this->DATABASE as $key => $item) {
			foreach ($item as $key => $coll) {
				foreach ($programs as $program) {
					if( isset($coll[10]) && $coll[10] == $program) $pcpu += $coll[2];
				}
			}
		}
		return $pcpu / $this->CPU_COUNT;
	}

	public function count()
	{
		return count($this->DATABASE);
	}
}
?>