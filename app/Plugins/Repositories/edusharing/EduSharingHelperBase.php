<?php

namespace App\Plugins\Repositories\edusharing;

use Exception;

class EduSharingHelperBase {
    public string $baseUrl;
    public string $privateKey;
    public string $appId;
    public string $language = 'de';
    private $curlHandler;

    /**
     * @param string $baseUrl
     * The base url to your repository in the format "http://<host>/edu-sharing"
     * @param string $privateKey
     * Your app's private key. This must match the public key registered in the repo
     * @param string $appId
     * Your app id name (as registered in the edu-sharing repository)
     */
    public function __construct(
        string $baseUrl,
        string $privateKey,
        string $appId
    )
    {
        if(!preg_match('/^([a-z]|[A-Z]|[0-9]|[-_])+$/', $appId))
        {
            throw new Exception('The given app id contains invalid characters or symbols');
        }
        if(substr($baseUrl, -1) === '/')
        {
            $baseUrl = substr($baseUrl, 0, -1);
        }
        $this->baseUrl=$baseUrl;
        $this->privateKey=$privateKey;
        $this->appId=$appId;
        $this->curlHandler=new DefaultCurlHandler();
    }

    public function registerCurlHandler(CurlHandler $handler)
    {
        $this->curlHandler = $handler;
    }

    public function handleCurlRequest(string $url, array $curlOptions)
    {
        return $this->curlHandler->handleCurlRequest($url, $curlOptions);
    }

    public function setLanguage(string $language)
    {
        $this->language = $language;
    }

    function sign(string $toSign)
    {
        $pkeyid = openssl_get_privatekey($this->privateKey);
        openssl_sign($toSign, $signature, $pkeyid);
        $signature = base64_encode($signature);
        @openssl_free_key($pkeyid);
        return $signature;
    }
}
