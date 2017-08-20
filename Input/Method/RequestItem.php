<?php
namespace X\Input\Method;
use X\Input\Functions;

class RequestItem extends Functions {
	public $name;

	function __construct($name, $default = false) {
		$this->name = $name;
		$this->getValue($name, $default);
	}

	public function is_set() {
		return isset($_REQUEST[$this->name]);
	}

	public function defaultValue($default) {
		$this->getValue($this->name, $default);
		return $this;
	}

	private function getValue($name, $default = false) {
		$this->default = $default;
		if (isset($_REQUEST[$name])) {
			$this->value = $_REQUEST[$name];
		} else {
			$this->value = $this->default;
		}
	}
}
?>