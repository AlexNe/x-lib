<?php
/**
* 
*/
class X_Input
{
	protected $Value;
	protected $Default = false;
	function __construct()
	{

	}

	public function lower_string()
	{
		return strtolower($this->string());
	}

	public function bool()
	{
		if (!is_string($this->Value)) return (bool) $this->Value;
		switch ( strtolower( trim( $this->Value ) ) ) 
		{
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

	public function str_replace($search, $replace)
	{
		return (is_string($this->Value))?str_replace($search, $replace, $this->Value):"";
	}

	public function explode($delimiter)
	{
		return (is_string($this->Value))?explode($delimiter, $this->Value):[];
	}

	public function int()
	{
		if($this->Default === false) $this->Default = 0;
		return intval($this->Value);
	}

	public function numeric()
	{
		return (is_numeric($this->Value)) ? $this->Value : 0;
	}

	public function float()
	{
		return floatval($this->Value);
	}

	public function string()
	{
		return strval($this->Value);
	}

	public function json_decode()
	{
		if($this->Value == null) return null;
		if($this->Value == false) return false;
		return json_decode($this->Value,true);
	}

	public function RequestValue( $Name, $Default=false )
	{
		return $this->Request( $Name, $Default )->Value;
	}

	public function Request( $Name, $Default=false )
	{
		return (new X_Input_RequestItem($Name, $Default));
	}

	public function PostValue( $Name, $Default=false )
	{
		return $this->Request( $Name, $Default )->Value;
	}

	public function Post( $Name, $Default=false )
	{
		return (new X_Input_MethodPostItem($Name, $Default));
	}

	public function GetValue( $Name, $Default=false )
	{
		return $this->Get( $Name, $Default )->Value;
	}

	public function Get( $Name, $Default=false )
	{
		return (new X_Input_MethodGetItem($Name, $Default));
	}

	public function CookieValue( $Name, $Default=false )
	{
		return $this->Cookie( $Name, $Default )->Value;
	}

	public function Cookie( $Name, $Default=false )
	{
		return (new X_Input_MethodCookieItem($Name, $Default));
	}
}
?>