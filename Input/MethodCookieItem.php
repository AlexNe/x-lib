<?php
/**
* 
*/
class X_Input_MethodCookieItem extends X_Input
{
	public $Name;
	public $Value;
	protected $Default = false;

	function __construct( $Name, $Default=false )
	{
		$this->Name = $Name;
		$this->get_value( $Name, $Default );
	}
	
	public function  is_set()
	{
		return isset($_COOKIE[ $this->Name ]);
	}

	public function DefaultValue($Default)
	{
		$this->get_value( $this->Name, $Default );
		return $this;
	}

	private function get_value( $name, $default=false )
	{
		$this->Default = $default;
		if ( isset( $_COOKIE[ $name ] ) )
			$this->Value = $_COOKIE[ $name ];
		else
			$this->Value = $this->Default;
	}
}
?>