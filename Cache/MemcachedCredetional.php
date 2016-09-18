<?php
namespace X\Cache;
use \X\ETrace\System as ESys;

class MemcachedCredetional_ConfigError extends ESys {}
class MemcachedCredetional_ConfigTypeError extends ESys {}
class MemcachedCredetional {
	/**
	 * @var mixed
	 */
	protected $prefix, $servers = [];
	/**
	 * @param $prefix
	 * @param array     $servers
	 */
	public function __construct($prefix = 'x_lib', $servers = ["localhost", 11211, 100]) {
		$this->prefix = $prefix;
		if (isset($servers[0])) {
			$type = gettype($servers[0]);
			switch ($type) {
				case 'string':
					$host   = (isset($servers[0])) ? $servers[0] : "localhost";
					$port   = (isset($servers[1])) ? $servers[1] : 11211;
					$weight = (isset($servers[2])) ? $servers[2] : 100;
					$this->addServer($host, $port, $weight);
					break;
				case 'array':
					if (is_string($servers[0][0])) {
					} else {
						throw new MemcachedCredetional_ConfigTypeError("Unknow type", 1, $servers);
					}
					break;

				default:
					throw new MemcachedCredetional_ConfigTypeError("Unknow type", 1, $servers);
					break;
			}
		}
	}

	/**
	 * @param $value
	 */
	public function addServer($host, $port = 11211, $weight = 100) {
		$this->servers[] = [$host, $port, $weight];
	}

	/**
	 * @param $value
	 */
	public function addServers($servers) {
		if (is_array($servers) && count($servers) > 0) {
			foreach ($servers as $server) {
				if (is_string($server[0])) {
					$host   = (isset($server[0])) ? $server[0] : "localhost";
					$port   = (isset($server[1])) ? $server[1] : 11211;
					$weight = (isset($server[2])) ? $server[2] : 100;
					$this->addServer($host, $port, $weight);
				} else {
					throw new MemcachedCredetional_ConfigTypeError("Unknow type", 1, $servers);
				}
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * @return mixed
	 */
	public function get_servers() {
		return $this->servers;
	}
}
?>