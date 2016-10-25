<?php
namespace X\ETrace;

/**
 *
 */
class EItem extends \Exception {

	/**
	 * @var mixed
	 */
	protected $context;
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
	protected $host;
	/**
	 * @var mixed
	 */
	protected $type;
	/**
	 * @var mixed
	 */
	protected $session_id;
	/**
	 * @var mixed
	 */
	protected $object_name;

	/**
	 * @param enum   $type      paranoid | trace | system | fatal | error | notification
	 * @param string $message
	 * @param int    $code      [= 1]
	 * @param string $file      [= false]
	 * @param int    $line      [= false]
	 * @param array  $context   [= []]
	 */
	/**
	 * @return mixed
	 */
	public function __construct($type, $message, $code = 1, $file = false, $line = false, $context = []) {
		parent::__construct($message, intval($code));
		if ( ! ($file === false)) {
			$this->file = $file;
		}

		if ( ! ($line === false)) {
			$this->line = $line;
		}

		$this->type        = $type;
		$this->context     = $context;
		$this->hash        = $this->calcHash();
		$this->host        = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "no.host";
		$this->session_id  = md5(microtime());
		$this->object_name = (new \ReflectionClass($this))->getName();
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
	 * @param $context
	 */
	public function setContext($context) {
		$this->context = $context;
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
			"hash"        => $this->hash,
			"host"        => $this->host,
			"type"        => $this->type,
			"code"        => $this->code,
			"file"        => $this->file,
			"line"        => $this->line,
			"count"       => $this->count,
			"trace"       => $this->Trace(),
			"message"     => $this->message,
			"context"     => $this->context,
			"object_name" => $this->object_name,
		];
	}

	public function increment() {
		$this->count++;
	}

	/**
	 * @return mixed
	 */
	public function count() {
		return $this->count;
	}

	public function clean_context() {
		$this->context = [];
	}

	/**
	 * @return hex
	 */
	public function getHash() {
		return $this->hash;
	}

	/**
	 * @param  $uid
	 * @return mixed
	 */
	private function calcHash() {
		return md5(serialize([
			$this->object_name,
			$this->host,
			$this->type,
			$this->message,
			$this->code,
			$this->file,
			$this->line,
			$this->Trace()]));
	}
}
?>