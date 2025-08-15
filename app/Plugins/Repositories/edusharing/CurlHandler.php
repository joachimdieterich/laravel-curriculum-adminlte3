<?php

namespace App\Plugins\Repositories\edusharing;

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
