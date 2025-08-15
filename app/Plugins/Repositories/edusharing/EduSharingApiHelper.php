<?php
namespace App\Plugins\Repositories\edusharing;

use Exception;

class EduSharingApiHelper extends EduSharingHelperAbstract  {

    /**
     * @param $ticket
     *  A ticket with the user session who is creating this usage
     * @param $repository
     * repository e.g. "-home-"
     * @param $nodeId
     * The edu-sharing node id
     * @return mixed
     */
    public function getRendering(
        $ticket,
        $repository,
        $nodeId
    )
    {
        if(!is_guest()){
            $headers =  $this->getSignatureHeaders($ticket);
            $headers[] = $this->getRESTAuthenticationHeader($ticket);
        }
        $curl = $this->base->handleCurlRequest(
            $this->repoUrl.'/rest/rendering/v1/details/'.$repository.'/'.$nodeId,
            [
                CURLOPT_FAILONERROR => false,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => $headers ?? '',
                CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
                CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
            ]
        );

        return json_decode($curl->content, true);
    }

    /**
     * @param $ticket
     *  A ticket with the user session who is creating this usage
     * @param $repository
     * repository e.g. "-home-"
     * @param $params
     * @return mixed
     */
    public function getSearchCustom(
        $ticket,
        $repository,
        $params
    )
    {
        $headers = $this->getSignatureHeaders($ticket);
        $headers[] = $this->getRESTAuthenticationHeader($ticket);
        $curl = $this->base->handleCurlRequest(
            $this->repoUrl.'/rest/search/v1/custom/'.$repository.'?'.http_build_query($params),
            [
                CURLOPT_FAILONERROR => false,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
                CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
            ]
        );

        return json_decode($curl->content, true);
    }

    /**
     * @param $ticket
     *  A ticket with the user session who is creating this usage
     * @param $repository
     * repository e.g. "-home-"
     * @param $parentId
     * The edu-sharing parent node id
     * @param $params
     * @return string
     */
    public function getChildren(
        $ticket,
        $repository,
        $parentId,
        $params
    )
    {
        $headers = $this->getSignatureHeaders($ticket);
        $headers[] = $this->getRESTAuthenticationHeader($ticket);

        $curl = $this->base->handleCurlRequest(
            $this->repoUrl.'/rest/node/v1/nodes/'.$repository.'/'.$parentId.'/children?'.http_build_query($params),
            [
                CURLOPT_FAILONERROR => false,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
                CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
            ]
        );

        return json_decode($curl->content, true);
    }

    /**
     * @param $ticket
     *  A ticket with the user session who is creating this usage
     * @param $repository
     * repository e.g. "-home-"
     * @param $params
     * @return string
     */
    public function getSearchQueriesV2(
        $ticket,
        $repository,
        $params
    )
    {
        switch ($params['filter']) {
            case '3':  $filter = 'cm:creator';                             //user_files
                $filter_value = auth()->user()->common_name;
                break;
            case '2':                                                      // shared_files
            case '1':                                                      // public_files
            default:   $filter = '';
                $filter_value = '';
                break;

        }
        if ($filter != '')
        {
            $postFields = [
                'criteria' => [
                    [
                        'property' => $params['property'],
                        'values' => [
                            $params['value']
                        ],
                    ],
                    [
                        'property' => $filter,
                        'values' => [
                            $filter_value,
                        ],
                    ],
                ],
            ];
        }
        else
        {
            $postFields = [
                'criteria' => [
                    [
                        'property' => $params['property'],
                        'values' => [
                            $params['value']
                        ],
                    ],
                    /* todo: check effect of search_context for results
                    array (
                        'property' => "ccm:search_context",
                        'values' => array("rlp-curriculum")
                    )*/
                ],
            ];
        }

        //dump(json_encode ( $postFields ));
        //dump($this->repoUrl . '/rest/search/v1/queries/' . $repository.'/-default-/curriculum?'.http_build_query($params));

        if(!is_guest()){
            $headers =  $this->getSignatureHeaders($ticket);
            $headers[] = $this->getRESTAuthenticationHeader($ticket);
        } else {
            $ts = time() * 1000;
            $toSign = $this->base->appId . $ticket . $ts;
            $signature = $this->sign($toSign);
            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
                'X-Edu-App-Id: ' . $this->base->appId,
                'X-Edu-App-Signed: ' . $toSign,
                'X-Edu-App-Sig: ' . $signature,
                'X-Edu-App-Ts: ' . $ts,
            ];
        }

        $curl = $this->base->handleCurlRequest(
            $this->base->baseUrl.'/rest/search/v1/queries/'.$repository.'/-default-/curriculum?'.http_build_query($params),
            [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($postFields),
                CURLOPT_FAILONERROR => false,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => $headers ?? [],
                CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
                CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
            ]
        );

        return json_decode($curl->content, true);
    }

}
