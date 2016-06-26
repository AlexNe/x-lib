<?php
namespace X\ETrace;

class Fatal extends EItem
{
	/**
	 * @var mixed
	 */
	protected $severity;
	/**
	 * Для функции register_shutdown_function(). Для отлова критических ошибок.
	 * Используется отлов последней ошибки error_get_last() котора подходит под условие критической ошибки.
	 *
	 * @param $message
	 * @param $severity
	 * @param $file
	 * @param false       $line
	 * @param array       $context
	 */
	public function __construct($message, $severity = 0, $file = false, $line = false)
	{
		parent::__construct("fatal", $message, 0, $file, $line, []);
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
}
?>