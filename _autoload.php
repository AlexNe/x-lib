<?php
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
spl_autoload_register(
	function ($className) {
		$classPath = explode('_', $className);
		if ( array_shift( $classPath ) != 'X') return;
		if ( ctype_digit( end( $classPath ) ) ) $V = "_" . array_pop($classPath); else $V = ""; // Version Control
		$filePath = dirname(__FILE__) . DS . implode(DS, $classPath) . $V . '.php';
		if ( count($classPath) == 0 ) $classPath[] = "X";
		if ( file_exists($filePath) ) require_once($filePath);
	}
);
?>