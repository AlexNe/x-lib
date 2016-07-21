<?php
namespace X\Tool\BC;

trait BcHexDec {
/**
 * @param $hex
 */
	protected function bchexdec($hex) {
		if (strlen($hex) == 1) {
			return hexdec($hex);
		} else {
			$remain = substr($hex, 0, -1);
			$last   = substr($hex, -1);
			return bcadd(bcmul(16, $this->bchexdec($remain)), hexdec($last));
		}
	}
}

?>