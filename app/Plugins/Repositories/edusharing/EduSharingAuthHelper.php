<?php
namespace App\Plugins\Repositories\edusharing;

class EduSharingAuthHelper extends EduSharingHelperAbstract  {

    /**
     * Gets detailed information about a ticket
     * Will throw an exception if the given ticket is not valid anymore
     * @param string $ticket
     * The ticket, obtained by @getTicketForUser
     * @return array
     * Detailed information about the current session
     * @throws Exception
     * Thrown if the ticket is not valid anymore
     */
    public function getTicketAuthenticationInfo(string $ticket) {
        $curl = $this->base->handleCurlRequest($this->base->baseUrl . '/rest/authentication/v1/validateSession', [
            CURLOPT_HTTPHEADER => [
                $this->getRESTAuthenticationHeader($ticket),
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5
        ]);
        $data = json_decode($curl->content, true);
        if ( is_null( $data ) ) {
            throw new Exception( 'No answer from repository. Possibly a timeout while trying to connect to ' . $this->base->baseUrl );
        }
        if($data['statusCode'] !== 'OK') {
            throw new Exception('The given ticket is not valid anymore');
        }
        return $data;
    }

    /**
     * Fetches the edu-sharing ticket for a given username
     * @param string $username
     * The username you want to generate a ticket for
     * @return string
     * The ticket, which you can use as an authentication header, see @getRESTAuthenticationHeader
     * @throws Exception
     */
    public function getTicketForUser(string $username) {

        $curl = $this->base->handleCurlRequest($this->base->baseUrl . '/rest/authentication/v1/appauth/' . rawurlencode($username), [
            CURLOPT_POST => 1,
            CURLOPT_FAILONERROR => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->getSignatureHeaders($username),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5
        ]);
        $data = json_decode($curl->content, true);
        if ($curl->error === 0 && $curl->info["http_code"] === 200 && ($data['userId'] === $username ||
                substr($data['userId'], 0, strlen($username) + 1) === $username . '@'
            )) {
            return $data['ticket'];
        } else {
            if ( is_null( $data ) ) {
                $data = ['error' => 'No answer from repository. Possibly a timeout while trying to connect to "' . $this->base->baseUrl . '"'];
            }
            if(isset($data['message'])) {
                throw new Exception($data['message']);
            }
            throw new Exception('edu-sharing ticket could not be retrieved: HTTP-Code ' .
                $curl->info["http_code"] . ': ' . $data['error'] . '/' . @$data['message']);
        }
    }
}
