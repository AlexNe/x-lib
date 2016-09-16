<?php
namespace X\ETrace;

class Import extends EItem {
	/**
	 * @var array
	 */
	protected $trace;

	/**
	 * @param $exception
	 */
	public function __construct($exception) {
		parent::__construct("system",
			$exception->getMessage(),
			$exception->getCode(),
			$exception->getFile(),
			$exception->getLine(), []);

		$this->trace       = $exception->getTrace();
		$this->object_name = (new \ReflectionClass($exception))->getName();

		if ($exception instanceof \PDOException) {
			$this->context["MySQL_Driver"] = $exception->errorInfo;
		}
	}

	/**
	 * @return mixed
	 */
	public function Trace() {
		return $this->trace;
	}
}
?>