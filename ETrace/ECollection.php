<?php
namespace X\ETrace;

class ECollection implements \IteratorAggregate, \ArrayAccess, \Countable {
	/**
	 * @var mixed
	 */
	protected $time, $microtime, $session_id;
	/**
	 * Хранилище объектов
	 * @var array
	 */
	protected $__collection = [];
	/**
	 * @var array
	 */
	protected $context_collection = [];
	public function __construct() {
		$this->time       = time();
		$this->microtime  = microtime();
		$this->session_id = md5($this->microtime);
	}

	// --------------------------------------------------------------------
	public function Serialize() {
		return serialize($this);
	}

	/**
	 * Проверяет тип объекта.
	 * Препятствует добавлению в коллекцию объектов `чужого` типа.
	 *
	 * @param  object      $object Объект для проверки
	 * @throws Exception
	 * @return void
	 */
	private function __check_type(&$object) {
		if ( ! ($object instanceof EItem)) {
			throw new \Exception('Объект типа `' . get_class($object)
				. '` не может быть добавлен в коллекцию объектов типа `' . $this->__type . '`');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Добавляет в коллекцию объекты, переданные в аргументах.
	 *
	 * @param  object(s) Объекты
	 * @return mixed     Collection
	 */
	public function add() {
		$args = func_get_args();
		foreach ($args as $object) {
			$this->offsetSet(null, $object);
		}
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Удаляет из коллекции объекты, переданные в аргументах.
	 *
	 * @param  object(s) Объекты
	 * @return mixed     Collection
	 */
	public function remove() {
		$args = func_get_args();
		foreach ($args as $object) {
			unset($this->__collection[array_search($object, $this->__collection)]);
		}
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Очищает коллекцию.
	 *
	 * @return mixed Collection
	 */
	public function clear() {
		$this->__collection = [];
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Выясняет, пуста ли коллекция.
	 *
	 * @return bool
	 */
	public function isEmpty() {
		return empty($this->__collection);
	}

	// --------------------------------------------------------------------

	/**
	 * Реализация интерфейса IteratorAggregate
	 */
	/**
	 * Возвращает объект итератора.
	 *
	 * @return CollectionIterator
	 */
	public function getIterator() {
		return new \ArrayIterator($this->__collection);
	}

	// --------------------------------------------------------------------

	/**
	 * Реализация интерфейса ArrayAccess.
	 */
	/**
	 * Sets an element of collection at the offset
	 *
	 * @param  ineter $offset Offset
	 * @param  mixed  $offset Object
	 * @return void
	 */
	public function offsetSet($offset, $object) {
		$this->__check_type($object);
		$offset = $object->getHash();
		if (count($object->getContext()) > 0) {
			$this->context_collection[$offset][] = $object->getContext();
			$object->clean_context();
		}
		if (isset($this->__collection[$offset])) {
			$this->__collection[$offset]->increment();
		} else {
			$this->__collection[$offset] = $object;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Выясняет существует ли элемент с данным ключом.
	 *
	 * @param  integer $offset Ключ
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($this->__collection[$offset]);
	}

	// --------------------------------------------------------------------

	/**
	 * Удаляет элемент, на который ссылается ключ $offset.
	 *
	 * @param  integer $offset Ключ
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->__collection[$offset]);
	}

	// --------------------------------------------------------------------

	/**
	 * Возвращает элемент по ключу.
	 *
	 * @param  integer $offset Ключ
	 * @return mixed
	 */
	public function offsetGet($offset) {
		if (isset($this->__collection[$offset]) === FALSE) {
			return NULL;
		}
		return $this->__collection[$offset];
	}

	// --------------------------------------------------------------------

	/**
	 * Реализация интерфейса Countable
	 */
	/**
	 * Возвращает кол-во элементов в коллекции.
	 *
	 * @return integer
	 */
	public function count() {
		return sizeof($this->__collection);
	}
}
?>