<?php
namespace App\Plugins\Repositories\edusharing;

use Exception;

class EduSharingNodeHelper extends EduSharingHelperAbstract  {
    /**
     * creates a usage for a given node
     * The given usage can later be used to fetch this node REGARDLESS of the actual user
     * The usage gives permanent access to this node and acts similar to a license
     * In order to be able to create an usage for a node, the current user (provided via the ticket)
     * MUST have CC_PUBLISH permissions on the given node id
     * @param string $ticket
     * A ticket with the user session who is creating this usage
     * @param string $containerId
     * A unique page / course id this usage refers to inside your system (e.g. a database id of the page you include the usage)
     * @param string $resourceId
     * The individual resource id on the current page or course this object refers to
     * (you may enumerate or use unique UUID's)
     * @param string $nodeId
     * The edu-sharing node id the usage shall be created for
     * @param string|null $nodeVersion
     * Optional: The fixed version this usage should refer to
     * If you leave it empty, the usage will always refer to the latest version of the node
     * @return Usage
     * An usage element you can use with @getNodeByUsage
     * Keep all data of this object stored inside your system!
     */
    public function createUsage(
        string $ticket,
        string $containerId,
        string $resourceId,
        string $nodeId,
        string $nodeVersion = null
    ) {
        $headers = $this->getSignatureHeaders($ticket);
        $headers[] = $this->getRESTAuthenticationHeader($ticket);
        $curl = $this->base->handleCurlRequest($this->base->baseUrl . '/rest/usage/v1/usages/repository/-home-', [
            CURLOPT_FAILONERROR => false,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode([
                'appId' => $this->base->appId,
                'courseId' => $containerId,
                'resourceId' => $resourceId,
                'nodeId' => $nodeId,
                'nodeVersion' => $nodeVersion,
            ]),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
            CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
        ]);
        $data = json_decode($curl->content, true);
        if ($curl->error === 0 && $curl->info["http_code"] === 200)
        {
            return new Usage(
                $data['parentNodeId'],
                $nodeVersion,
                $containerId,
                $resourceId,
                $data['nodeId']
            );
        }
        else
        {
            throw new Exception('creating usage failed ' .
                $curl->info["http_code"] . ': ' . $data['error'] . ' ' . $data['message']);
        }
    }

    /**
     * @DEPRECATED
     * Returns the id of an usage object for a given node, container & resource id of that usage
     * This is only relevant for legacy plugins which do not store the usage id and need to fetch it in order to delete an usage
     * @param string $ticket
     * A ticket with the user session who is creating this usage
     * @param string $containerId
     * A unique page / course id this usage refers to inside your system (e.g. a database id of the page you include the usage)
     * @param string $resourceId
     * The individual resource id on the current page or course this object refers to
     * (you may enumerate or use unique UUID's)
     * @return string
     * The id of the usage, or NULL if no usage with the given data was found
     */
    public function getUsageIdByParameters(
        $ticket,
        $nodeId,
        $containerId,
        $resourceId
    ){
        $headers = $this->getSignatureHeaders($ticket);
        $headers[] = $this->getRESTAuthenticationHeader($ticket);
        $curl = $this->base->handleCurlRequest($this->base->baseUrl . '/rest/usage/v1/usages/node/' . rawurlencode($nodeId), [
            CURLOPT_FAILONERROR => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
            CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
        ]);
        $data = json_decode($curl->content, true);
        if ($curl->error === 0 && $curl->info["http_code"] === 200)
        {
            foreach($data["usages"] as $usage)
            {
                if($usage["appId"] == $this->base->appId && $usage["courseId"] == $containerId && $usage["resourceId"] == $resourceId)
                {
                    return $usage["nodeId"];
                }
            }
            return null;
        }
        else
        {
            throw new Exception('fetching usage list for course failed ' .
                $curl->info["http_code"] . ': ' . $data['error'] . ' ' . $data['message']);
        }
    }

    /**
     * Loads the edu-sharing node refered by a given usage
     * @param $usage
     * The usage, as previously returned by @createUsage
     * @param string $displayMode
     * The displayMode
     * This will ONLY change the content representation inside the "detailsSnippet" return value
     * @param array $renderingParams
     * @return mixed
     * Returns an object containing a "detailsSnippet" repesentation
     * as well as the full node as provided by the REST API
     * Please refer to the edu-sharing REST documentation for more details
     * @throws Exception
     */
    public function getNodeByUsage(
        $usage,
        $displayMode = DisplayMode::Inline,
        array $renderingParams = null
    )
    {
        $url = $this->base->baseUrl . '/rest/rendering/v1/details/-home-/' . rawurlencode($usage->nodeId);
        $url .= '?displayMode=' . rawurlencode($displayMode);
        if($usage->nodeVersion)
        {
            $url .= '&version=' . rawurlencode($usage->nodeVersion);
        }

        $headers = $this->getSignatureHeaders($usage->usageId);
        $headers[] = 'X-Edu-Usage-Node-Id: ' . $usage->nodeId;
        $headers[] = 'X-Edu-Usage-Course-Id: ' . $usage->containerId;
        $headers[] = 'X-Edu-Usage-Resource-Id: ' . $usage->resourceId;

        $curl = $this->base->handleCurlRequest($url, [
            CURLOPT_FAILONERROR => false,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($renderingParams),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
            CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
        ]);

        $data = json_decode($curl->content, true);
        if ($curl->error === 0 && $curl->info["http_code"] === 200)
        {
            return $data;
        }
        else if ($curl->info["http_code"] === 403)
        {
            throw new Exception('the given usage is deleted and the requested node is not public');
        }
        else if ($curl->info["http_code"] === 404)
        {
            throw new Exception('the given node is already deleted ' .
                $curl->info["http_code"] . ': ' . $data['error'] . ' ' . $data['message']);
        }
        else
        {
            throw new Exception('fetching node by usage failed ' .
                $curl->info["http_code"] . ': ' . $data['error'] . ' ' . $data['message']);
        }
    }

    /**
     * Deletes the given usage
     * We trust that you've validated if the current user in your context is allowed to do so
     * There is no restriction in deleting usages even from foreign users, as long as they were generated by your app
     * Thus, this endpoint does not require any user ticket
     * @param string $nodeId
     * The edu-sharing node id this usage belongs to
     * @param string $usageId
     * The usage id
     */
    public function deleteUsage(
        string $nodeId,
        string $usageId
    ) {
        $headers = $this->getSignatureHeaders($nodeId.$usageId);
        $curl = $this->base->handleCurlRequest(
            $this->base->baseUrl . '/rest/usage/v1/usages/node/' . rawurlencode($nodeId) . '/' . rawurlencode($usageId),
            [
                CURLOPT_FAILONERROR => false,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
                CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
            ]
        );
        $data = json_decode($curl->content, true);
        if ($curl->error === 0 && $curl->info["http_code"] === 200)
        {
        }
        else if ($curl->info["http_code"] === 404)
        {
            throw new Exception('the given usage is already deleted or does not exist');
        }
        else
        {
            throw new Exception('deleting usage failed ' .
                $curl->info["http_code"] . ': ' . $data['error'] . ' ' . $data['message']);
        }

    }

    /**
     * Function getRedirectUrl
     *
     * @param string $mode
     * @param $usage
     * @return string
     * @throws JsonException
     * @throws NodeDeletedException
     * @throws UsageDeletedException
     * @throws Exception
     */
    public function getRedirectUrl(string $mode, $usage): string {
        $headers = $this->getUsageSignatureHeaders($usage);
        $node    = $this->getNodeByUsage($usage);
        $params  = '';
        foreach ($headers as $header) {
            if (!str_starts_with($header, 'X-')) {
                continue;
            }
            $header = explode(': ', $header);
            $params .= '&' . $header[0] . '=' . urlencode($header[1]);
        }
        if ($mode === 'content') {
            $url    = $node['node']['content']['url'] ?? '';
            $params .= '&closeOnBack=true';
            //$params .= '&closeOnBack=true&repository=' . $node['node']['ref']['repo'];
        } else if ($mode === 'download') {
            $url = $node['node']['downloadUrl'] ?? '';
        } else {
            throw new Exception('Unknown parameter for mode: ' . $mode);
        }
        return $url . (str_contains($url, '?') ? '' : '?') . $params;
    }

    /**
     * Function getUsageSignatureHeaders
     *
     * @param $usage
     * @return array
     */
    private function getUsageSignatureHeaders($usage): array {
        $headers   = $this->getSignatureHeaders($usage->usageId);
        $headers[] = 'X-Edu-Usage-Node-Id: ' . $usage->nodeId;
        $headers[] = 'X-Edu-Usage-Course-Id: ' . $usage->containerId;
        $headers[] = 'X-Edu-Usage-Resource-Id: ' . $usage->resourceId;
        return $headers;
    }

    /**
     * Function getPreview
     *
     * @param Usage $usage
     * @return CurlResult
     */
    public function getPreview(Usage $usage): CurlResult {
        $url = $this->base->baseUrl . '/preview?nodeId=' . rawurlencode($usage->nodeId) . '&maxWidth=400&maxHeight=400&crop=true';

        if ($usage->nodeVersion !== null) {
            $url .= '&version=' . rawurlencode($usage->nodeVersion);
        }

        $headers = $this->getUsageSignatureHeaders($usage);

        return $this->base->handleCurlRequest($url, [
            CURLOPT_FAILONERROR    => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYHOST => env('EDUSHARING_CURLOPT_SSL_VERIFYHOST', 2),
            CURLOPT_SSL_VERIFYPEER => env('EDUSHARING_CURLOPT_SSL_VERIFYPEER', 1),
        ]);
    }

}
