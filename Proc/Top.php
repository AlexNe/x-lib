<?php
class X_Proc_Top
{
	private $CPU_COUNT = 0;
	private $CMD = "/usr/bin/top -s 2 -CPSand 5 9999999999999999999999";
	private $PROCESSES = array();
	private $DATABASE = array();
	private $RAW;
	private $RAW_LINES;

	function __construct( $spy_processes = [] )
	{
		$this->RAW = `$this->CMD`;
		$this->RAW_LINES = explode("\n", $this->RAW);
		$is_head = true;

		foreach ($this->RAW_LINES as $key => $value) 
		{
			$line = $this->del_spaces($value);
			
			$data = explode(" ", trim($line));
			if(count($data)>1)
			{
				if($data[0] == "last" && $data[1] == "pid:") $is_head = true;
				else if($data[0] == "PID") $is_head = false;
				if($data[0] == "CPU") $this->CPU_COUNT++;
				if( $is_head ) $this->parse_head( $data, $line );
				else  $this->parse_body( $data, $line, $spy_processes );
			}
			//$this->DATABASE[] = $cols;
		}
	}

	public function get_database()
	{
		return $this->DATABASE;
	}


/*
  PID USERNAME      THR PRI NICE   SIZE    RES STATE   C   TIME     CPU COMMAND
   11 root            2 155 ki31     0K    16K RUN     1 855.4H 166.85% [idle]
30250 www            27  20    0   303M   138M kqread  1 129:19   7.67% /usr/local/sbin/httpd -DNOHTTPACCEPT
13998 www            27  20    0   287M   127M lockf   0  32:00   5.76% /usr/local/sbin/httpd -DNOHTTPACCEPT
48665 mysql          30  24    0  2484M   196M uwait   0 141.6H   5.57% [mysqld]
59704 www            27  20    0   299M   116M lockf   0  26:27   0.39% /usr/local/sbin/httpd -DNOHTTPACCEPT
*/
	private function parse_body( $data, $line, $spy_processes )
	{
		$process_argv = implode(" ", array_slice( $data, 11 ) );

		foreach ($spy_processes as $spy_process) 
		{
			if( !(strpos($process_argv, $spy_process) === false) )
			{
				$this->data_set( "processes", "cpu", $spy_process, ($this->transform_to_raw($data[10])/$this->CPU_COUNT) );
				$this->data_set( "processes", "memory", $spy_process, ($this->transform_to_raw($data[5])/$this->CPU_COUNT) );
			}
		}
	}
/*
last pid: 58146;  load averages:  1.01,  1.15,  1.16  up 26+18:54:17    22:38:13
103 processes: 2 running, 100 sleeping, 1 waiting
CPU 0:  0.0% user,  0.0% nice,  100% system,  0.0% interrupt,  0.0% idle
CPU 1:  0.0% user,  0.0% nice,  0.0% system,  0.0% interrupt,  100% idle
Mem: 917M Active, 802M Inact, 234M Wired, 18M Cache, 90M Buf, 29M Free
Swap: 3852M Total, 1880M Used, 1972M Free, 48% Inuse
*/
	private function parse_head( $data, $line )
	{
		if($data[1] == "processes:") 
		{
			$this->data_set( "system", "processes", "all", $data[0] );
			$this->data_set( "system", "processes", "running", $data[2] );
			$this->data_set( "system", "processes", "sleeping", $data[4] );
			$this->data_set( "system", "processes", "waiting", $data[6] );
		}
		else if($data[0] == "CPU")
		{
			$sub_data = explode( ",", ( explode(":", $line)[1] ) );
			foreach ($sub_data as $value) 
			{
				$ex_value = explode(" ", trim($value));
				$this->data_set( "system", "cpu", "cpu_".trim($data[1],":")."_".strtolower($ex_value[1]), $this->transform_to_raw($ex_value[0]) );
			}
		}
		else if($data[0] == "Mem:")
		{
			$sub_data = explode( ",", trim(explode(":", $line)[1]) );
			foreach ($sub_data as $value) 
			{
				$ex_value = explode(" ", trim($value));
				$this->data_set( "system", "memory", strtolower($ex_value[1]), $this->transform_to_raw($ex_value[0]) );
			}
		}		
		else if($data[0] == "Swap:")
		{
			$sub_data = explode( ",", trim(explode(":", $line)[1]) );
			foreach ($sub_data as $value) 
			{
				$ex_value = explode(" ", trim($value));
				$this->data_set( "system", "swap", strtolower($ex_value[1]), $this->transform_to_raw($ex_value[0]) );
			}
		}
	}

	private function data_set($system, $cat, $name, $value)
	{
		$value = round($value);
		$count_systems = ["max","avg","min"];
		foreach ($count_systems as $count_system) 
		{
			if( isset($this->DATABASE[$system][$count_system][$cat][$name]) )
			{
				$TMP = $this->DATABASE[$system][$count_system][$cat][$name];
				switch ($count_system) 
				{
					case 'max':
						$this->DATABASE[$system][$count_system][$cat][$name] = ($TMP > $value) ? $TMP : $value;
					break;
					case 'avg':
						$this->DATABASE[$system][$count_system][$cat][$name] = round( ( $TMP + $value ) / 2 );
					break;
					case 'min':
						$this->DATABASE[$system][$count_system][$cat][$name] = ($TMP < $value) ? $TMP : $value;
					break;
				}
			}
			else $this->DATABASE[$system][$count_system][$cat][$name] = $value;
		}
	}

	private function transform_to_raw($data)
	{
		$data = trim($data," ");
		switch ( strtoupper( substr($data, -1, 1) ) ) 
		{
			case 'G':
				return ( trim($data, "G") * 1024 * 1024 * 1024 );
			break;

			case 'M':
				return ( trim($data, "M") * 1024 * 1024 );
			break;

			case 'K':
				return ( trim($data, "K") * 1024 );
			break;

			case '%':
				return trim($data, "%");
			break;
			
			default:
				return trim($data);
			break;
		}
	}

	private function del_spaces($line)
	{
		while ( !(strpos($line, "  ") === false) ) 
		{
			$line = str_replace("  ", " ", $line);
		}
		return $line;
	}
}
?>