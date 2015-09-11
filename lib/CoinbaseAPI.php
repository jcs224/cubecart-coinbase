<?php require "Curl.php";

use Curl\Curl;

class CoinbaseAPI {

    public function __construct($apiKey, $apiSecret, $coinbaseUrl) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->coinbaseUrl = $coinbaseUrl;
    }

    public function call($function, $request_type = "GET", $fields = null) {
        $url = "https://api." . $this->coinbaseUrl . ".com/v2/" . $function;
        $time = time();

        if ($fields) {
            $message = $time . $request_type . "/v2/" . $function . json_encode($fields);
        } else {
            $message = $time . $request_type . "/v2/" . $function;
        }

        $signature = hash_hmac("sha256", $message, $this->apiSecret);

        if ($fields) {
            $fields_json = json_encode($fields);
        } else {
            $fields_json = null;
        }

        $curl = new Curl();
        $curl->setHeader("Content-Type", "application/json");
        $curl->setHeader("CB-ACCESS-KEY", $this->apiKey);
        $curl->setHeader("CB-ACCESS-SIGN", $signature);
        $curl->setHeader("CB-ACCESS-TIMESTAMP", $time);

        if ($request_type == "GET") {
            $curl->get($url, $fields_json);
        } elseif ($request_type == "POST") {
            $curl->post($url, $fields_json);
        }

        return $curl->response;
    }
}