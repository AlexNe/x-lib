<?php
namespace X\Input;

class Input {
	public function requestValue($name, $default = false) {
		return $this->request($name, $default)->value();
	}

	public function request($name, $default = false) {
		return (new Method\RequestItem($name, $default));
	}

	public function postValue($name, $default = false) {
		return $this->post($name, $default)->value();
	}

	public function post($name, $default = false) {
		return (new Method\PostItem($name, $default));
	}

	public function getValue($name, $default = false) {
		return $this->get($name, $default)->value();
	}

	public function get($name, $default = false) {
		return (new Method\GetItem($name, $default));
	}

	public function cookieValue($name, $default = false) {
		return $this->cookie($name, $default)->value();
	}

	public function cookie($name, $default = false) {
		return (new Method\CookieItem($name, $default));
	}
}
?>