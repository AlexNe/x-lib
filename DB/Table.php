<?php
namespace X\DB;
class Table
{
	private $driver;
	function __construct($driver) { $this->driver = $driver; }
	public function __call($name, $arguments) 
	{
		return new TableItem($name, $this->driver);		
    }
}
?>