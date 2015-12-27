<?php
class X_HTTP_ProxyList
{
	public $check_url = "http://test.arhat.tv/ok";
	public $check_resp = "ok";
	public $data_dir = "./";

	private $reject_list = [];
	private $fail_list = [];
	private $used_list = [];

	private $database = [];
	private $service_list = [];
	private $offset = 0;
	function __construct($data_dir)
	{
		if(is_dir($data_dir)) $this->data_dir = $data_dir;
		$this->database = $this->load_database();
		$this->reject_list = $this->load_reject();
		$this->fail_list = $this->load_fail();
		foreach ($this->fail_list as $IP => $value) 
		{
			if($value < time() - (60*60*24*20)) unset($this->fail_list[$IP]);
		}
	}

	public function set_fail($IP)
	{
		$this->database = array_merge($this->database, $this->load_database());
		$this->fail_list = array_merge($this->fail_list, $this->load_fail());
		$this->fail_list[$IP] = time();
		unset($this->database[$IP]);
		$this->save_database();
		$this->save_fail();
	}
	public function set_reject($IP)
	{
		$this->database = array_merge($this->database, $this->load_database());
		$this->reject_list = array_merge($this->reject_list, $this->load_reject());
		$this->reject_list[$IP] = time();
		$this->database = array_merge($this->database, $this->load_database());
		$this->database[$IP]["status"] = "REJECT";
		$this->database[$IP]["time"] = time();
		$this->save_database();
		$this->save_reject();
	}
	public function unset_reject($IP)
	{
		$this->reject_list = array_merge($this->reject_list, $this->load_reject());
		$this->database = array_merge($this->database, $this->load_database());
		$this->database[$IP]["status"] = "REJECT";
		$this->database[$IP]["time"] = time();
		$this->save_database();
		unset($this->reject_list[$IP]);
		$this->save_reject();
	}
	public function get_next($R=false)
	{
		if($R) $this->get_list();
		if(count($this->database) == 0) $this->get_list();
		shuffle_assoc($this->database);
		foreach ($this->database as $IP => $DATA) 
		{
			if(isset($this->fail_list[$IP])) $this->set_fail($IP);
			if(isset($this->reject_list[$IP]) && $this->reject_list[$IP] < (time()-(60*60*24*10))) $this->unset_reject($IP);

			if(
				isset($this->database[$IP]) &&  
				!isset($this->used_list[$IP]) && 
				!isset($this->reject_list[$IP]) && 
				!isset($this->fail_list[$IP]) 
				)
			{
				if($this->database[$IP]["status"] == "NEW") 
				{
					$this->database[$IP]["status"] = $this->check_proxy($IP)?"OK":"FAIL";
					//ec("Proxy > ".$IP." > ".$this->database[$IP]["status"]);
				}
				if($this->database[$IP]["status"] == "OK" )
				{
					//ec("Use proxy > ".$IP);
					$this->database[$IP]["time"] = time();
					$this->used_list[$IP] = true;
					$this->save_database();
					return $IP;
				}
				if($this->database[$IP]["status"] == "FAIL") $this->set_fail($IP);
			}
		}
		if($R) return false; else return $this->get_next(true);
	}
##############################
	public function load_database()
	{
		if(is_file($this->data_dir."proxyList.txt"))
		{
			$data = unserialize(file_get_contents($this->data_dir."proxyList.txt"));
			while (!is_array($data)) 
			{
				$data = unserialize(file_get_contents($this->data_dir."proxyList.txt"));
				if($data == false) $data = [];
				sleep(1);
			}
			return $data;
		}
		else return array();
	}
	public function save_database()
	{
		file_put_contents($this->data_dir."proxyList.txt", serialize($this->database));
	}
##############################
	public function load_fail()
	{
		if(is_file($this->data_dir."proxyList-fail.txt"))
		{
			$data = unserialize(file_get_contents($this->data_dir."proxyList-fail.txt"));
			while (!is_array($data)) 
			{
				$data = unserialize(file_get_contents($this->data_dir."proxyList-fail.txt"));
				if($data == false) $data = [];
				sleep(1);
			}
			return $data;
		}
		else return array();
	}
	public function save_fail()
	{
		file_put_contents($this->data_dir."proxyList-fail.txt", serialize($this->fail_list));
	}
##############################
	public function load_reject()
	{
		if(is_file($this->data_dir."proxyList-reject.txt"))
		{
			$data = unserialize(file_get_contents($this->data_dir."proxyList-reject.txt"));
			while (!is_array($data)) 
			{
				$data = unserialize(file_get_contents($this->data_dir."proxyList-reject.txt"));
				if($data == false) $data = [];
				sleep(1);
			}
			return $data;
		}
		else return array();
	}
	public function save_reject()
	{
		file_put_contents($this->data_dir."proxyList-reject.txt", serialize($this->reject_list));
	}
##############################
	public function get_list()
	{
		foreach ($this->service_list as $Service) 
		{
			$list = $Service->get();
			shuffle($list);
			foreach ($list as $value) 
			{
				if(
					!isset($this->database[$value]) &&
					!isset($this->fail_list[$value]) //&&	!isset($this->reject_list[$value])
					) 
					$this->database[$value] = ["IP" => $value, "status" => "NEW", "time" => time()];
			}
		}
	}
	
	public function add_service()
	{
		$arg_list = func_get_args();
		foreach ($arg_list as $key => $value) 
		{
			$this->service_list[] = new $value();
		}
	}

	public function check_proxy($proxy)
	{
		if( trim( (new X_HTTP_Client(["Proxy" => $proxy, "timeout" => 5]))->GET($this->check_url) ) == $this->check_resp )
			return true;
		else return false;
	}
}

?>