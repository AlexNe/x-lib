<?php
namespace X\Tool;

trait BitwiseCyclicShift {

	/**
	 * @param  $v
	 * @param  $c
	 * @return mixed
	 */
	protected function BitwiseCROR($v, $c) {
		$c = $c % 32;
		return $c ? ((($v >> 1) & 2147483647) >> ($c - 1)) | ($v << (32 - $c)) : $v;
	}

	/**
	 * @param  $v
	 * @param  $c
	 * @return mixed
	 */
	protected function BitwiseCROL($v, $c) {
		$c = $c % 32;
		return $c ? ($v << $c) | ((($v >> 1) & 2147483647) >> (31 - $c)) : $v;
	}
}

?>