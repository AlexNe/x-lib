<?php
namespace X\Database\Driver;
use \X\ETrace\System as ESys;

class PDOStatement {
	/**
	 * @var mixed
	 */
	protected $db;
	/**
	 * @var mixed
	 */
	protected $prepare;

	/**
	 * @param $db
	 */
	public function __construct($db, $sql) {
		$this->db      = $db;
		$this->prepare = $this->db->prepare($sql);
	}

	/**
	 * @param $parameter
	 * @param $value
	 * @param $data_type
	 */
	public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR) {
		return $this->prepare->bindValue($parameter, $value, $data_type);
	}

	/**
	 * @param $input_parameters
	 */
	public function execute($input_parameters = null) {
		if ($input_parameters == null) {
			return $this->prepare->execute();
		} else {
			return $this->prepare->execute($input_parameters);
		}
	}

	/**
	 * @param  $fetch_style
	 * @param  null               $cursor_orientation
	 * @param  \PDOFETCH_ORI_NEXT $cursor_offset
	 * @return mixed
	 */
	public function fetch($fetch_style = null, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0) {
		if ($fetch_style == null) {
			return $this->prepare->fetch();
		} else {
			return $this->prepare->fetch($fetch_style, $cursor_orientation, $cursor_offset);
		}
	}

	/**
	 * @param  $fetch_style
	 * @param  null           $fetch_argument
	 * @param  array          $ctor_args
	 * @return mixed
	 */
	public function fetchAll($fetch_style = null, $fetch_argument = null, $ctor_args = []) {
		if ($fetch_style == null) {
			return $this->prepare->fetchAll();
		} else if ($fetch_argument == null) {
			return $this->prepare->fetchAll($fetch_style);
		} else {
			return $this->prepare->fetchAll($fetch_style, $fetch_argument, $ctor_args);
		}
	}

	/**
	 * @return mixed
	 */
	public function rowCount() {
		return $this->prepare->rowCount();
	}

	/**
	 * @return mixed
	 */
	public function columnCount() {
		return $this->prepare->columnCount();
	}

	/**
	 * @param  $attribute
	 * @param  $value
	 * @return mixed
	 */
	public function setAttribute($attribute, $value) {
		return $this->prepare->setAttribute($attribute, $value);
	}

	/**
	 * @param  $mode
	 * @return mixed
	 */
	public function setFetchMode($mode) {
		return $this->prepare->setFetchMode($mode);
	}

	public function __sleep() {return [];}

	public function __wakeup() {}
}
?>