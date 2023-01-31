<?php

class CurlResult {
    public $content;
    public $error;
    public $info;
    public function __construct(
        string $content,
        int $error,
        array $info
    ) {
        $this->content = $content;
        $this->error = $error;
        $this->info = $info;
    }
}

/**
 * Class that describes the handling of curl requests
 */
abstract class CurlHandler {
    /**
     * @param string $url the request url
     * @param array $curlOptions the curl options, assoc array same as in the default php curl implementation
     * @return CurlResult a result object containing the response content, error/status code and a curl info array
     */
    public abstract function handleCurlRequest(string $url, array $curlOptions): CurlResult;
}

/**
 * The default curl handler. It uses the native php curl functions
 * Use this as a reference for your custom curl library usage
 */
class DefaultCurlHandler extends CurlHandler {
    public function handleCurlRequest(string $url, array $curlOptions): CurlResult {
        $curl = curl_init($url);
        curl_setopt_array($curl, $curlOptions);
        $content = curl_exec($curl);
        $error     = curl_errno( $curl );
        $info = curl_getinfo($curl);
        curl_close($curl);
        return new CurlResult($content, $error, $info);
    }
}