<?php
namespace X\Network\Http;
class ClientResponse {
	/**
	 * @var mixed
	 */
	protected $curl_result;
	/**
	 * @var mixed
	 */
	protected $header_size;
	/**
	 * @var mixed
	 */
	protected $header;
	/**
	 * @var mixed
	 */
	protected $body;
	/**
	 * @param $curl_result
	 */
	public function __construct($header_size, $curl_result) {
		$this->curl_result = $curl_result;
		$this->header_size = $header_size;
		$this->header      = substr($curl_result, 0, $header_size);
		$this->body        = substr($curl_result, $header_size);
	}

	/**
	 * @return mixed
	 */
	public function get_header_size() {
		return $this->header_size;
	}

	/**
	 * @return mixed
	 */
	public function get_header() {
		return $this->header;
	}

	/**
	 * @return mixed
	 */
	public function get_body() {
		return $this->body;
	}

	public function get_json() {
		return json_decode($this->body, true);
	}
}
?>