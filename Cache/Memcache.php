<?php
namespace X\Cache;

class Memcache
{
	/**
	 * @var mixed
	 */
	protected $compression = false;
	/**
	 * @var mixed
	 */
	protected $prefix;
	/**
	 * @var mixed
	 */
	protected $mcache = false;
	/**
	 * @param $prefix
	 * @param $host
	 * @param $port
	 * @param $compression
	 */
	public function __construct($prefix, $host = "localhost", $port = 11211, $compression = false)
	{
		if (class_exists("\memcache"))
		{
			$this->prefix      = $prefix;
			$this->compression = $compression;
			$this->mcache      = new \memcache();

			$this->mcache->connect($host, $port)
			|| $this->mcache = false;
		}
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $expire
	 */
	public function set($key, $value, $expire = 240)
	{
		if ( ! $this->mcache)
		{
			return $this->mcache;
		}

		$key = $this->prefix . $key;
		return $this->mcache->set(md5($key), $value, $this->compression, $expire);
	}

	/**
	 * @param $key
	 */
	public function get($key)
	{
		if ( ! $this->mcache)
		{
			return $this->mcache;
		}

		$key = $this->prefix . $key;
		return $this->mcache->get(md5($key), $this->compression);
	}
}
?>