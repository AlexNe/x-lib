<?php
namespace X\Tool;

trait IP {
	protected function get_ip() {
		if (isset($_SERVER["REMOTE_ADDR"])) {
			return $_SERVER["REMOTE_ADDR"];
		} else {
			return false;
		}
	}
}
?>