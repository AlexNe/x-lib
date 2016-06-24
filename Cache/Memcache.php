<?php
namespace X\Cache;

class Memcache extends \memcache
{
	protected $compression = false;
	protected $prefix;
	protected $mcache = false;
	function __construct( $prefix, $host = "localhost", $port = 11211, $compression = false )
	{
		$this->prefix = $prefix;
		$this->compression = $compression;
		$this->connect($host,$port);
	}

	public function set( $key, $value, $expire = 240 )
	{
		$key = $this->prefix . $key;
		return parent::set( md5($key), $value, $this->compression, $expire);
	}

	public function get($key)
	{
		$key = $this->prefix . $key;
		return parent::get( md5($key), $this->compression);
	}
}
?>