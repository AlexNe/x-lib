<?php
namespace X\Network\Http;
class Client extends ClientSettings {

	/**
	 * @return mixed
	 */
	public function exec($url = false) {
		if ($url) {
			$this->parse_url($url);
		}

		if (isset($this->data["useragent"]) && is_string($this->data["useragent"])) {
			$headers[] = "User-Agent: {$this->data["useragent"]}";
		}

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->build_url());

		if (isset($this->data["cookies"]) && is_array($this->data["cookies"]) && count($this->data["cookies"]) > 0) {
			curl_setopt($curl, CURLOPT_COOKIE, $this->implode_cookies());
		}

		if (isset($headers)) {
			curl_setopt($curl, CURLOPT_HEADER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		}
		#if ($this->Proxy) {
		#	curl_setopt($curl, CURLOPT_PROXY, $this->Proxy);
		#}

		if (isset($this->data["post"]) && is_array($this->data["post"]) && count($this->data["post"]) > 0) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->build_post());
		}

		curl_setopt($curl, CURLOPT_TIMEOUT, $this->data["timeout"]);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_HEADER, true);
		//curl_setopt($curl, CURLOPT_VERBOSE, 1);

		$result      = curl_exec($curl);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		curl_close($curl);
		return new ClientResponse($header_size, $result);
	}
}

?>