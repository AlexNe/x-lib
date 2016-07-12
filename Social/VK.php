<?php
namespace X\Social;

class VK {
    /**
     * @var string
     */
    protected $api_url = "";

    public function __construct() {}

    /**
     * @param string $method
     * @param array  $params
     */
    public function api($method, $params = []) {

        $Client = new X\Network\Http\Client($this->api_url . "/" . $method);
        $Client->set_model_data(["post" => $params]);
        if ($data = $Client->exec()->json_decode()) {
            return $data;
        }
        return;
    }
}?>