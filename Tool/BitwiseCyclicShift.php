<?php
namespace X\Tool;

trait BitwiseCyclicShift {

	/**
	 * Побитовый циклический сдвиг вправо (32bit)
	 * @param  int   $v value
	 * @param  int   $c count
	 * @return int
	 */
	protected function BitwiseCROR($v, $c) {
		$c = $c % 32;
		return $c ? ((($v >> 1) & 2147483647) >> ($c - 1)) | ($v << (32 - $c)) : $v;
	}

	/**
	 * Побитовый циклический сдвиг влево (32bit)
	 * @param  int   $v value
	 * @param  int   $c count
	 * @return int
	 */
	protected function BitwiseCROL($v, $c) {
		$c = $c % 32;
		return $c ? ($v << $c) | ((($v >> 1) & 2147483647) >> (31 - $c)) : $v;
	}
}

?>