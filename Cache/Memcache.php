<?php
namespace X\Cache;

class Memcache {

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
	 */
	public function __construct($prefix, $host = "localhost", $port = 11211) {
		if (class_exists("\memcache")) {
			$this->prefix = $prefix;
			$this->mcache = new \memcache();

			$this->mcache->connect($host, $port)
			|| $this->mcache = false;
		}
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $expire
	 */
	public function set($key, $value, $expire = 240) {
		if ( ! $this->mcache) {
			return $this->mcache;
		}

		$key = $this->prefix . $key;
		return $this->mcache->set($key, $value, 0, $expire);
	}

	/**
	 * @param $key
	 */
	public function get($key) {
		if ( ! $this->mcache) {
			return $this->mcache;
		}

		$key = $this->prefix . $key;
		return $this->mcache->get($key, 0);
	}
}
?>