<?php
namespace X\ETrace;

/**
 *
 */
class EItem extends \Exception {
	/**
	 * @var int
	 */
	protected $count = 1;
	/**
	 * @var mixed
	 */
	protected $hash;

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
	/**
	 * @return mixed
	 */
	public function __construct($type, $message, $code = 0, $file = false, $line = false, $context = []) {
		parent::__construct($message, $code);
		if ( ! ($file === false)) {
			$this->file = $file;
		}

		if ( ! ($line === false)) {
			$this->line = $line;
		}

		$this->context = $context;
		$this->hash    = $this->calcHash();
	}

	/**
	 * @return mixed
	 */
	public function __debugInfo() {
		return $this->Model();
	}

	/**
	 * @return mixed
	 */
	public function Trace() {
		return $this->getTrace();
	}

	/**
	 * @return mixed
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @return mixed
	 */
	public function Serialize() {
		return serialize($this->Model());
	}

	public function Model() {
		return
			[
			"hash"    => $this->hash,
			"message" => $this->message,
			"code"    => $this->code,
			"file"    => $this->file,
			"line"    => $this->line,
			"count"   => $this->count,
			"trace"   => $this->Trace(),
			"context" => $this->context,
			"globals" => $GLOBALS,
		];
	}

	public function increment() {
		$this->count++;
	}

	/**
	 * @return hex
	 */
	public function getHash() {
		return $this->hash;
	}

	private function calcHash() {
		return md5(serialize([$this->message, $this->code, $this->file, $this->line, $this->Trace()]));
	}
}
?>