<?php

/**
 *
 */
class X_HTTP_Client {
	public $UserAgent = "Mozilla/5.0 (Windows NT 6.3; rv:41.0) Gecko/20100101 Firefox/41.0";

	public $HEADERS = [];

	public $Cookies = [];

	public $Proxy = false;

	public $Timeout = 60;

	public function __construct($cfg = []) {
		foreach ($cfg as $key => $value) {
			switch (strtolower($key)) {
				case 'proxy':
					$this->Proxy = $value;
					break;
				case 'useragent':
					$this->UserAgent = $value;
					break;
				case 'cookies':
					$this->Cookies = $value;
					break;
				case 'headers':
					$this->HEADERS = $value;
					break;
				case 'timeout':
					$this->Timeout = $value;
					break;
			}
		}
	}

	public function GET($URL) {
		$this->HEADERS[] = "User-Agent: " . $this->UserAgent;
		$cookies_arr     = [];
		foreach ($this->Cookies as $k => $v) {$cookies_arr[] = $k . "=" . $v;}
		$result = false;
		$curl   = curl_init();
		curl_setopt($curl, CURLOPT_URL, $URL);
		//curl_setopt($curl, CURLOPT_HEADER, true);
		if (count($cookies_arr) > 0) {
			curl_setopt($curl, CURLOPT_COOKIE, implode("; ", $cookies_arr));
		}

		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->HEADERS);
		if ($this->Proxy) {
			curl_setopt($curl, CURLOPT_PROXY, $this->Proxy);
		}

		curl_setopt($curl, CURLOPT_TIMEOUT, $this->Timeout);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

	public function POST($URL) {
		# NOT YET
	}
}
?>