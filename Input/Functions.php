<?php
/**
 * Created by PhpStorm.
 * User: xfile
 * Date: 20.08.2017
 * Time: 15:48
 */
namespace X\Input;

class Functions {
	protected $value;
	protected $default = false;

	public function lower_string() {
		return strtolower($this->string());
	}

	public function bool() {
		if ( ! is_string($this->value)) {
			return (bool) $this->value;
		}

		switch (strtolower(trim($this->value))) {
			case 'true':
			case 'on':
			case 'yes':
			case 'y':
			case '1':
				return true;
				break;
			default:
				return false;
				break;
		}
	}

	public function str_replace($search, $replace) {
		return (is_string($this->value)) ? str_replace($search, $replace, $this->value) : "";
	}

	public function explode($delimiter) {
		return (is_string($this->value)) ? explode($delimiter, $this->value) : [];
	}

	public function int() {
		if ($this->default === false) {
			$this->default = 0;
		}

		return intval($this->value);
	}

	public function numeric() {
		return (is_numeric($this->value)) ? $this->value : 0;
	}

	public function float() {
		return floatval($this->value);
	}

	public function string() {
		return strval($this->value);
	}

	public function json_decode() {
		if ($this->value == null) {
			return null;
		}

		if ($this->value == false) {
			return false;
		}

		return json_decode($this->value, true);
	}

	public function value() {
		return $this->value;
	}
}