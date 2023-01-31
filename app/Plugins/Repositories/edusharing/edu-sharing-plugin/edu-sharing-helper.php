<?php

namespace App\Plugins\Repositories\edusharing;
require_once 'Curl.php';

class EduSharingHelper {
    /**
     * generate a new key pair (private + public) to be registered in the edu-sharing repository
     * Store the data somewhere in your application, e.g. database
     * use the public key returned to register the application in edu-sharing
     * NOTE: This function will fail on windows-based systems!
     * @throws Exception
     */
    public static function generateKeyPair(
    )
    {
        $res = openssl_pkey_new();
        if(!$res) {
            throw new Exception("No result from openssl_pkey_new. Please check your php installation");
        }
        openssl_pkey_export($res, $privatekey);
        $publickey = openssl_pkey_get_details($res);
        $publickey = $publickey["key"];
        return [
            "privatekey" => $privatekey,
            "publickey" => $publickey
        ];
    }

    /**
     * Generates an edu-sharing compatible xml file for registering the application
     * This is a very basic function and is only intended for demonstration or manual use. Data is not escaped!
     */
    public static function generateEduAppXMLData(string $appId, string $publickey, string $type = 'LMS', string $publicIP = '*') {
        return '<?xml version="1.0" encoding="UTF-8"?>
                <!DOCTYPE properties SYSTEM "http://java.sun.com/dtd/properties.dtd">
                <properties>
                    <entry key="appid">' . $appId . '</entry>
                    <entry key="public_key">' . $publickey . '</entry>
                    <entry key="type">' . $type . '</entry>
                    <entry key="domain"></entry>
                    <!-- in case of wildcard host: Replace this, if possible, with the public ip from your service -->
                    <entry key ="host">' . $publicIP . '</entry>
                    <!-- must be true -->
                    <entry key="trustedclient">true</entry>
                </properties>
                ';
    }

}
