<?php
require_once "TItem.php";
require_once "Error.php";

//throw new ErrorException("Error Processing Request", 1);

class wwwww
{

	/**
	 * @var mixed
	 */
	static $eer;
	/**
	 * @param $value
	 * @param $rrrr
	 */
	public function qwewqew($value, $rrrr)
	{
		$ee = new X\ETrace\Error("Error Processing Request", 1);
		print_r(
			$ee->Trace()
		);
	}

	public static function qqq()
	{
		self::$eer = new self();
		self::fff();

	}

	public static function fff()
	{
		self::$eer->qwewqew([1, 2, 5, 7, 9], "dfgd");

	}
}

wwwww::qqq();

//throw $ddd;

?>