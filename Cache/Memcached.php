<?php
namespace X\Cache;

class Memcached {

	/**
	 * @var mixed
	 */
	protected $prefix;
	/**
	 * @var mixed
	 */
	public $link = false;

	/**
	 * @param $prefix
	 * @param $host
	 * @param $port
	 */
	public function __construct(MemcachedCredetional $Credetional) {
		if (class_exists("\Memcached")) {
			$this->prefix = $Credetional->get_prefix();
			$this->link   = new \Memcached($this->prefix);
			$this->link->addServers($Credetional->get_servers());
		}
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $expire
	 */
	public function set($key, $value, $expire = 240) {
		if ( ! $this->link) {
			return $this->link;
		}

		$key = $this->prefix . $key;
		return $this->link->set($key, $value, $expire);
	}

	/**
	 * @param $key
	 */
	public function get($key) {
		if ( ! $this->link) {
			return $this->link;
		}

		$key = $this->prefix . $key;
		return $this->link->get($key);
	}
}
?>