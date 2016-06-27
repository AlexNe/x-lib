<?php
namespace X\ETrace;

class Import extends EItem {
	/**
	 * @var array
	 */
	protected $trace;
	/**
	 * @var string
	 */
	protected $original;

	/**
	 * @param string $original
	 * @param string $message
	 * @param int    $code
	 * @param string $file
	 * @param int    $line
	 * @param array  $trace
	 */
	public function __construct($original, $message, $code = 0, $file = false, $line = false, $trace = []) {
		parent::__construct("system", $message, $code, $file, $line, []);
		$this->original = $original;
		$this->trace    = $trace;
	}

	/**
	 * @return array
	 */
	public function Trace() {
		return $this->trace;
	}

	/**
	 * @return array
	 */
	public function Model() {
		return array_merge(["original" => $this->original], parent::Model());
	}
}
?>