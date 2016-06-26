<?php
namespace X\ETrace;

/**
 *
 */
class TItem extends \Exception
{
	/**
	 * @var mixed
	 */
	protected $Trace;
	/**
	 * @var mixed
	 */
	protected $context;

	/**
	 * @param enum   $type      paranoid | trace | system | fatal | error | notification
	 * @param string $message
	 * @param int    $code      [= 0]
	 * @param string $file      [= false]
	 * @param int    $line      [= false]
	 * @param array  $context   [= []]
	 */
	public function __construct($type, $message, $code = 0, $file = false, $line = false, $context = [])
	{
		parent::__construct($message, $code);
		if ( ! ($file === false))
		{
			$this->file = $file;
		}

		if ( ! ($line === false))
		{
			$this->line = $line;
		}

		$this->context = $context;
		$this->Trace   = $this->getTrace();
	}

	public function Save()
	{
		# code...
	}
}
?>