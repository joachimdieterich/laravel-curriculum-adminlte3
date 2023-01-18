<?php

namespace App\Plugins\Eventmanagement\eVewa;

use App\Plugins\Eventmanagement\EventmanagementPlugin;

/**
 * Description of plugin
 *
 * @author joachimdieterich
 */
class eVewa extends EventmanagementPlugin
{
    const PLUGINNAME = 'eVewa';

    private $user;

    private $password;

    private $url;

    private $sessionId;

    private $proxy = false;

    private $proxy_port = false;

    public function about()
    {
        return 'eVewa plugin';
    }

    public function __construct()
    {
        $this->user = env('EVEWA_API_USER', '');
        $this->password = env('EVEWA_API_PASSWORD', '');
        $this->url = env('EVEWA_API_URL', '');

        $this->getSessionId();

        $this->proxy = env('EVEWA_PROXY', false);
        $this->proxy = env('EVEWA_PROXY_PORT', false);
    }

    /**
     * Gibt eine Liste an Veranstaltungen aus. Die Beschreibung der Parameter kann dem "definition"-Tag entnommen werden.
     * Dort befinden sich ebenfalls alle Lookup-Daten.
     *
     * @param  array  $params
     * @return object
     */
    public function lesePlrlpVeranstaltungen($params)
    {

        $search = $params['search'];

//            $params = array(
//                'method'=> 'lesePlrlpVeranstaltungen',
//                'session_id' => (string) $this->sessionId,
//                'mandant'=> '',
//                'page'=> '',
//                'limit'=> '',
//                'order'=> '',
//                'search'=> $search, //todo better search in evewa!
//                'abgeschlossene'=> '',
//                'gs_ort'=> '',
//                'gs_plz'=> '',
//                'veranstalter'=> '',
//                'von'=> '',
//                'bis'=> '',
//                'schulartentag'=> '',
//                'zielgruppentag'=> '',
//            );
        //dump($params) ;

        $params = array_replace_recursive( //replace defaults with given params
             [
                 'method' => 'lesePlrlpVeranstaltungen',
                 'session_id' => (string) $this->sessionId,
                 'mandant' => '',
                 'page' => '',
                 'limit' => '',
                 'order' => '',
                 'search' => '',
                 'abgeschlossene' => '',
                 'gs_ort' => '',
                 'gs_plz' => '',
                 'veranstalter' => '',
                 'von' => '',
                 'bis' => '',
                 'schulartentag' => '',
                 'zielgruppentag' => '',
             ], $params);
//

        $raw = $this->call($this->url, 'GET', http_build_query($params));

        return simplexml_load_string($raw);
    }

    /**
     * Gibt detailierte Daten einer Veranstaltung aus.
     *
     * @param  type  $params
     * @return object
     */
    public function lesePlrlpVeranstaltungDetail($params)
    {
        $params = array_replace_recursive( //replace defaults with given params
             [
                 'method' => 'lesePlrlpVeranstaltungDetail',
                 'session_id' => (string) $this->sessionId,
                 'mandant' => '',
                 'artikelnr' => '',
             ], $params);

        $raw = $this->call($this->url, 'GET', http_build_query($params));

        return simplexml_load_string($raw);
    }

    private function isLoggedIn()
    {
        $postFields = 'method=isloggedin&session_id='.$this->sessionId;
        $raw = $this->call($this->url, 'GET', $postFields);

        return (simplexml_load_string($raw)->isloggedin->code == 200) ? true : false;
    }

    private function getSessionId()
    {
        $postFields = 'method=login&user='.$this->user.'&pw='.$this->password;
        $raw = $this->call($this->url, 'GET', $postFields);
        $this->sessionId = isset(simplexml_load_string($raw)->login) ? simplexml_load_string($raw)->login->session_id : false;
    }

    private function call($url, $httpMethod = '', $postFields = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  //timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds

        if ($this->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            if ($this->proxy_port) {
                curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);
            }
        }

        switch ($httpMethod) {
            case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);
                    break;
            default: break;
        }

        if (! empty($postFields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        $exec = curl_exec($ch);

        if ($exec === false) {
            error_log($url.' ---> '.curl_error($ch).' ---> Error-Code:'.curl_errno($ch)); // for debugging
                //throw new Exception ( curl_error ( $ch ) );
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode != 200) {
            //deal with it
        }
        curl_close($ch);

        return $exec;
    }
}
