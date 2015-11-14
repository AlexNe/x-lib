<?php
class X_Exception_List
{
	const DB_MySQLi_NULL_CONSTRUCT_ARGUMENT = 11020;
	const DB_MySQLi_WRONG_CONSTRUCT_ARGUMENT = 11021;

	static function GetList()
	{
		$Reflection = X_Exception_List::Reflection();
		return $Reflection->getConstants();
	}

	static function Reflection()
	{
		return new ReflectionClass('X_Exception_List');
	}
}
?>