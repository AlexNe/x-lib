<?php
class X_HTTP_ProxyParser_WebanetlabsNet
{
	public $URL = "http://webanetlabs.net/publ/24";
	public $IPs = array();
	private $body = "";
	function __construct()
	{

	}
	public function get()
	{
		$page = file_get_contents($this->URL);
		while(preg_match('/freeproxy\/proxylist_at_([0-9]+).([0-9]+).([0-9]+).txt/', $page, $o))
		{
			$this->body .= file_get_contents("http://webanetlabs.net/".$o[0]);
			$page = str_replace($o[0], "", $page);
		}
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