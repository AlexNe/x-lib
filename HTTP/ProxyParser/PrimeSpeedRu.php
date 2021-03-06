<?php
class X_HTTP_ProxyParser_PrimeSpeedRu
{
	public $URL = "http://www.prime-speed.ru/proxy/free-proxy-list/all-working-proxies.php";
	public $IPs = array();
	private $body = false;
	function __construct()
	{

	}
	public function get()
	{
		$this->body = file_get_contents($this->URL);
		while(preg_match('/([0-9]+).([0-9]+).([0-9]+).([0-9]+):([0-9]+)/', $this->body, $o))
		{
			$this->IPs[] = $o[0];
			$this->body = str_replace($o[0], "", $this->body);
		}
		return $this->IPs;
	}
	public function get_next()
	{
		return false;
	}
	public function get_all()
	{
		return $this->get();
	}
}
?>