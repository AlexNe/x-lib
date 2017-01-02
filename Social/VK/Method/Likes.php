<?php
namespace X\Social\VK\Method;
use X\Social\VK\VKAPI;

/**
 *
 */
class Likes extends VKAPI {
	protected $api_version = "5.60";
	/**
	 * https://vk.com/dev/likes.getList
	 */
	public function getList($type, $owner_id, $item_id, $filter = "likes", $extended = 0, $offset = 0, $count = 200, $skip_own = 0) {
		return $this->check_response($this->api("likes.getList", [
			"type"     => $type,
			"owner_id" => $owner_id,
			"item_id"  => $item_id,
			"filter"   => $filter,
			"extended" => $extended,
			"offset"   => $offset,
			"count"    => $count,
			"skip_own" => $skip_own,
			"v"        => $this->api_version
		]));
	}
}
?>