<?php
if ( ! defined('DS')) {define('DS', DIRECTORY_SEPARATOR);}

spl_autoload_register(
	function ($class_name) {
		$class_name = str_replace("\\", "_", $class_name);
		$class_path = explode('_', $class_name);
		if (array_shift($class_path) != 'X') {return;}
		if (ctype_digit(end($class_path))) {$v = "_" . array_pop($class_path);} else { $v = "";}
		// Version Control
		$file_path = dirname(__FILE__) . DS . implode(DS, $class_path) . $v . '.php';
		if (count($class_path) == 0) {$class_path[] = "X";}
		if (file_exists($file_path)) {require_once ($file_path);}
	}
);
?>