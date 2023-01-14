<?php

namespace App\Plugins\Repositories\edusharing;

use App\Config;
use SoapClient;
use SOAPHeader;

class EdusharingSoapClient extends SoapClient
{
    /**
     * Set app properties and soap headers
     *
     * @param  string  $wsdl
     * @param  array  $options
     */
    public function __construct($wsdl, $options = [])
    {
        ini_set('default_socket_timeout', 15);
        parent::__construct($wsdl, $options);
        $this->edusharing_set_soap_headers();
    }

    /**
     * Set soap headers
     *
     * @throws Exception
     */
    private function edusharing_set_soap_headers()
    {
        $appId = Config::where([
            ['referenceable_type', '=', 'App\Edusharing'],
            ['key', '=',  'appId'],
        ])->get()->first()->value;
        $privkey = Config::where([
            ['referenceable_type', '=', 'App\Edusharing'],
            ['key', '=',  'privateKey'],
        ])->get()->first()->value;

        try {
            $timestamp = round(microtime(true) * 1000);
            $signdata = $appId.$timestamp;
            $pkeyid = openssl_get_privatekey($privkey);
            openssl_sign($signdata, $signature, $pkeyid);
            $signature = base64_encode($signature);
            openssl_free_key($pkeyid);
            $headers = [];
            $headers[] = new SOAPHeader('http://webservices.edu_sharing.org', 'appId', $appId);
            $headers[] = new SOAPHeader('http://webservices.edu_sharing.org', 'timestamp', $timestamp);
            $headers[] = new SOAPHeader('http://webservices.edu_sharing.org', 'signature', $signature);
            $headers[] = new SOAPHeader('http://webservices.edu_sharing.org', 'signed', $signdata);
            parent::__setSoapHeaders($headers);
        } catch (Exception $e) {
            throw new Exception(get_string('error_set_soap_headers', 'edusharing').$e->getMessage());
        }
    }
}
