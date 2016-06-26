<?php
namespace X\ETrace;

class Error extends EItem
{
	/**
	 * @var mixed
	 */
	protected $severity;
	/**
	 * Для функции set_error_handler(). Для отлова ошибок.
	 *
	 * @param string $message
	 * @param int    $severity  [= 0]
	 * @param string $file      [= false]
	 * @param int    $line      [= false]
	 * @param array  $context   [= []]
	 */
	public function __construct($message, $severity = 0, $file = false, $line = false, $context = [])
	{
		parent::__construct("error", $message, 0, $file, $line, $context);
		$this->severity = $severity;
	}

	/**
	 * @return mixed
	 */
	public function getSeverity()
	{
		return $this->severity;
	}

	/**
	 * @return mixed
	 */
	public function Trace()
	{
		$trace = parent::Trace();
		array_shift($trace);
		return $trace;
	}

	public function Model()
	{
		return array_merge(["severity" => $this->severity], parent::Model());
	}
}
?>