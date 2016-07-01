<?php
namespace X\Network\Http;
class ClientSettings {

	/**
	 * @var array
	 */
	protected $url_data =
		[
		"scheme" => false,
		"host"   => false,
		"path"   => false,
		"query"  => false,
	];
	/**
	 * @var array
	 */
	protected $data =
		[
		"get"       => false,
		"post"      => false,
		"put"       => false,
		"timeout"   => 10,
		"cookies"   => false,
		"useragent" => false,
	];

	/**
	 * @param $data
	 */
	public function __construct($data = false) {
		if (is_string($data)) {
			$this->parse_url($data);
		} else if (is_array($data)) {
			$this->set_model_data($data);
		}
	}

	/**
	 * @param  $url
	 * @return mixed
	 */
	public function parse_url($url) {
		if (is_array($data = parse_url($url))) {
			$this->url_data = $data;
			if (isset($this->url_data["query"])) {
				$out = [];
				parse_str($this->url_data["query"], $out);
				if (is_array($this->data["get"])) {
					$this->data["get"] = array_merge($this->data["get"], $out);
				} else {
					$this->data["get"] = $out;
				}
			}
			return $this;
		}
	}

	/**
	 * @param $data
	 */
	public function set_model_data($data) {
		if (isset($data["url"])) {
			$this->parse_url($data["url"]);
		}
		if (isset($data["scheme"])) {
			$this->url_data["scheme"] = $data["scheme"];
		}
		if (isset($data["host"])) {
			$this->url_data["host"] = $data["host"];
		}
		if (isset($data["path"])) {
			$this->url_data["path"] = $data["path"];
		}
		if (isset($data["query"])) {
			$this->url_data["query"] = $data["query"];
		}
		if (isset($data["get"])) {
			$this->data["get"] = $data["get"];
		}
		if (isset($data["post"])) {
			$this->data["post"] = $data["post"];
		}
		if (isset($data["put"])) {
			$this->data["put"] = $data["put"];
		}
		if (isset($data["cookies"])) {
			$this->data["cookies"] = $data["cookies"];
		}
		if (isset($data["useragent"])) {
			$this->data["useragent"] = $data["useragent"];
		}
		if (isset($data["timeout"])) {
			$this->data["timeout"] = intval($data["timeout"]) ?: 10;
		}
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function add_getvar($name, $value) {
		$this->data["get"][$name] = $value;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function add_postvar($name, $value) {
		$this->data["post"][$name] = $value;
	}

	/**
	 * @param $value
	 */
	public function set_putvar($value) {
		$this->data["put"] = $value;
	}

	protected function build_url() {
		if (isset($this->url_data["scheme"]) && isset($this->url_data["host"]) && isset($this->url_data["path"])) {
			return "{$this->url_data["scheme"]}://{$this->url_data["host"]}{$this->url_data["path"]}" .
				(is_array($this->data["get"]) ? "?" . http_build_query($this->data["get"]) : "");
		} else {
			throw new \X\ETrace\System("bad url params");
		}
	}

	protected function build_post() {
		return (isset($this->data["post"]) && is_array($this->data["post"])) ? http_build_query($this->data["post"]) : "";
	}

	/**
	 * @param $data
	 */
	protected function implode_cookies() {
		$data = $this->data["cookies"];
		array_walk($data, function (&$i, $k) {$i = implode("=", [$k, $i]);});
		return implode("; ", $data);
	}
}

?>