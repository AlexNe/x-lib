<?php
namespace X\Social\VK\Method;
use X\Social\VK\VKAPI;

/**
 *
 */
class Wall extends VKAPI {
	protected $api_version = "5.60";
	/**
	 * https://vk.com/dev/wall.getReposts
	 */
	public function getReposts($owner_id, $post_id, $offset = 0, $count = 20) {
		return $this->check_response($this->api("wall.getReposts", [
			"owner_id" => $owner_id,
			"post_id"  => $post_id,
			"offset"   => $offset,
			"count"    => $count,
			"v"        => $this->api_version
		]));
	}

	/**
	 * https://vk.com/dev/wall.getById
	 * @param $posts              - Идентификаторы постов
	 * @param $extended           - выводить данные профилей
	 * @param $copy_history_depth - глубина репостов
	 * @param $fields
	 */
	public function getById($posts, $extended = 0, $copy_history_depth = 2, $fields = "") {
		return $this->check_response($this->api("wall.getById", [
			"posts"              => $posts,
			"extended"           => $extended,
			"copy_history_depth" => $copy_history_depth,
			"fields"             => $fields,
			"v"                  => $this->api_version
		]));
	}
}
?>