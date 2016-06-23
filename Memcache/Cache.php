<?php
namespace X\Memcache;

class Cache extends \Memcache
{
	protected $compression = false;
	protected $prefix;
	protected $connect = false;
	function __construct( $prefix, $host = "localhost", $port = 11211, $compression = false )
	{
		$this->prefix = $prefix;
		$this->compression = $compression;
		if( $this->connect($host,$port) ) $this->connect = true;
	}

	public function set( $key, $value, $expire = 240 )
	{
		$key = $this->prefix ."-". $key;
		return parent::set( $key , $value, $this->compression, $expire);
	}

	public function get($key)
	{
		$key = $this->prefix ."-". $key;
		return parent::get($key, $this->compression);
	}
}
?>