<?php

namespace App\Plugins\Repositories\edusharing;

use App\Config;
use App\Plugins\Repositories\RepositoryPlugin;
use App\RepositorySubscription;
use Illuminate\Support\Facades\Auth;

/**
 * Description of plugin
 *
 * @author joachimdieterich
 */
class Edusharing extends RepositoryPlugin
{
    const PLUGINNAME = 'edusharing';

    public $accessToken = null;

    private $grant_type;

    private $client_id;

    private $client_secret;

    private $repoUrl;

    private $repoUser;

    private $repoPwd;

    private $proxy = false;

    private $proxy_port = false;

    public function about()
    {
        return 'edu-sharing plugin';
    }

    public function __construct()
    {
        $this->grant_type = env('EDUSHARING_GRAND_TYPE', 'password');
        $this->client_id = env('EDUSHARING_CLIENT_ID', 'eduApp');
        $this->client_secret = env('EDUSHARING_CLIENT_SECRET', 'secret');
        $this->repoUrl = env('EDUSHARING_REPO_URL', '');
        $this->repoUser = env('EDUSHARING_REPO_USER', '');
        $this->repoPwd = env('EDUSHARING_REPO_PWD', '');
        $this->proxy = env('EDUSHARING_REPO_PROXY', false);
        $this->proxy_port = env('EDUSHARING_REPO_PROXY_PORT', false);

        if (isset(auth()->user()->id)) {
            $this->setTokens(); //do not set token here to prevent blankpage if edusharing is offline
        }
    }

    private function getPersonalToken()
    {
        //get ticket via soap
        $appId = Config::where([
            ['referenceable_type', '=', 'App\Edusharing'],
            ['key', '=',  'appId'],
        ])->get()->first()->value;
        $wsdl = Config::where([
            ['referenceable_type', '=', 'App\Edusharing'],
            ['key', '=',  'wsdl'],
        ])->get()->first()->value;
        $paramstrusted = ['applicationId' => $appId,
            'ticket' => session_id(), 'ssoData' => edusharing_get_auth_data(), ];

        try {
            $client = new EdusharingSoapClient($wsdl);

            $return = $client->authenticateByTrustedApp($paramstrusted);
            $this->accessToken = $return->authenticateByTrustedAppReturn->ticket;
        } catch (Exception $e) {
            trigger_error($e, E_USER_WARNING);
        }
    }

    private function setTokens()
    {
        if (optional(Config::where([
            ['referenceable_type', '=', 'App\Edusharing'],
            ['key', '=',  'accessMode'],
        ])->get()->first())->value == 'personal'
        and Auth::user()->id != env('GUEST_USER')) { //only get Token if authenticated with common_name
            $this->getPersonalToken();
        }
        /*else
        {
            $postFields = 'grant_type=' . $this->grant_type . '&client_id=' . $this->client_id . '&client_secret=' . $this->client_secret . '&username=' . $this->repoUser . '&password=' . $this->repoPwd;
            $raw        = $this->call ( $this->repoUrl . '/oauth2/token', 'POST', array (), $postFields );
            $return     = json_decode ( $raw );
            $this->accessToken = $return->access_token;
            return $return;
        }*/
    }

    public function getAbout()
    {
        $ret = $this->call($this->repoUrl.'/rest/_about');

        return json_decode($ret, true);
    }

    public function createUser($user)
    {
        $this->call(
            $this->repoUrl.'/rest/iam/v1/people/-home-/'.urlencode($user['username']),
            'POST',
            ['Content-Type: application/json'],
            json_encode($user['profile'])
        );
    }

    public function setCredential($user)
    {
        $this->call(
            $this->repoUrl.'/rest/iam/v1/people/-home-/'.urlencode($user['username']).'/credential',
            'PUT',
            ['Content-Type: application/json'],
            json_encode(['newPassword' => $user['password']])
        );
    }

    public function getGroup($group)
    {
        return json_decode($this->call($this->repoUrl.'/rest/iam/v1/groups/-home-/'.urlencode($group)), true);
    }

    public function createGroup($group)
    {
        $this->call(
            $this->repoUrl.'/rest/iam/v1/groups/-home-/'.urlencode($group['groupname']),
            'POST',
            ['Content-Type: application/json'],
            json_encode($group['properties'])
        );
    }

    public function addMember($group, $member)
    {
        $this->call($this->repoUrl.'/rest/iam/v1/groups/-home-/'.urlencode($group).'/members/'.urlencode($member), 'PUT');
    }

    public function loginToScope($userName, $password, $scope = '')
    {
        $ret = $this->call(
            $this->repoUrl.'/authentication/v1/loginToScope',
            'POST',
            ['Content-Type: application/json'],
            json_encode(
                [
                    'userName' => $userName,
                    'password' => $password,
                    'scope' => $scope,
                ]
            )
        );

        return json_decode($ret, true);
    }

    public function getUser($username = '-me-')
    {
        return $this->call($this->repoUrl.'/rest/iam/v1/people/-home-/'.urlencode($username));
    }

    public function createIoNode($title, $folderId)
    {
        $postFields = [
            [
                'name' => '{http://www.alfresco.org/model/content/1.0}name',
                'values' => [$title],
            ],
        ];

        return $this->call(
            $this->repoUrl.'/rest/node/v1/nodes/-home-/'.urlencode($folderId).'/children?type=%7Bhttp%3A%2F%2Fwww.campuscontent.de%2Fmodel%2F1.0%7Dio',
            'POST',
            ['Content-Type: application/json'],
            json_encode($postFields)
        );
    }

    public function addNodeContent($nodeId, $versionComment, $mimetype, $postFields)
    {
        return $this->call(
            $this->repoUrl.'/rest/node/v1/nodes/-home-/'.urlencode($nodeId).'/content?versionComment='.urlencode($versionComment).'&mimetype='.urlencode($mimetype),
            'POST',
            ['Content-Type: multipart/form-data'],
            $postFields
        );
    }

    public function setPermissions($nodeId, $permissions)
    {
        $postFields = [
            'inherited' => false,
            'permissions' => $permissions,
        ];
        $this->call(
            $this->repoUrl.'/rest/node/v1/nodes/-home-/'.$nodeId.'/permissions',
            'PUT', ['Content-Type: application/json'],
            json_encode($postFields)
        );
    }

    public function createFolder($title, $parentId)
    {
        $postFields = [
            [
                'name' => '{http://www.alfresco.org/model/content/1.0}name',
                'values' => [$title],
            ],
        ];
        $node = $this->call(
            $this->repoUrl.'/rest/node/v1/nodes/-home-/'.urlencode($parentId).'/children?type='.urlencode('{http://www.campuscontent.de/model/1.0}map'),
            'POST',
            ['Content-Type: application/json'],
                json_encode($postFields)
             );
        $return = json_decode($node);

        return $return->node->ref->id;
    }

    public function setOrganization($organization, $folderId)
    {
        $this->call(
            $this->repoUrl.'/rest/organization/v1/organizations/-home-/'.$organization.'?folder='.$folderId,
            'PUT',
            [],
            json_encode(['organization' => $organization, 'folder' => $folderId])
        );
    }

    public function getCompanyHome()
    {
        $return = json_decode($this->call($this->repoUrl.'/rest/node/v1/nodes/-home-?query='.urlencode('PATH:"/app:company_home"')), true);

        return $return['nodes'][0]['ref']['id'];
    }

    public function searchNodes($repository, $params)
    {
        $return = $this->call($this->repoUrl.'/rest/node/v1/nodes/'.$repository.'?'.http_build_query($params));

        return json_decode($return, true);
    }

    public function getPerson($person = '-me-')
    {
        $return = $this->call($this->repoUrl.'/rest/iam/v1/people/-home-/'.$person);

        return json_decode($return, true);
    }

    public function getAllGroups()
    {
        $ret = $this->call($this->repoUrl.'/rest/iam/v1/groups/-home-?pattern=*');

        return json_decode($ret, true);
    }

    public function getOrganizations()
    {
        $ret = $this->call($this->repoUrl.'/rest/organization/v1/organizations/-home-');

        return json_decode($ret, true);
    }

    private function call($url, $httpMethod = '', $additionalHeaders = [], $postFields = [])
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
            case 'PUT':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    break;
            case 'DELETE':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
            default: break;
        }

//        $headers = array_merge ( array (
//                        'Accept: application/json',
//                        'Authorization: Bearer ' . $this->accessToken
//        ), $additionalHeaders );
        if ($this->accessToken) {
            $headers = array_merge([
                'Accept: application/json',
                'Authorization: EDU-TICKET '.$this->accessToken,
            ], $additionalHeaders);
        } else {
            $headers = array_merge([
                'Accept: application/json',
            ], $additionalHeaders);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

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

    public function getCollections($repository, $collection)
    {
        $getFields = [
            'repository' => $repository,
            'collection' => $collection,
        ];
        $ret = $this->call(
            $this->repoUrl.'/rest/collection/v1/collection/'.$repository.'/permissions',
            'GET',
            ['Content-Type: application/json'],
            json_encode($getFields)
        );

        return json_decode($ret, true);
    }

    public function getRendering($repository, $node)
    {
        //dump($this->repoUrl . '/rest/rendering/v1/details/' . $repository . '/' . $node);
        $ret = $this->call($this->repoUrl.'/rest/rendering/v1/details/'.$repository.'/'.$node);

        return json_decode($ret, true);
    }

    //https://[EDUSHARINGDOMAIN]/edu-sharing/rest/search/v1/custom/-home-?contentType=ALL&property=cm:name&value=curriculum&maxItems=10&skipCount=0
    //https://[EDUSHARINGDOMAIN]/edu-sharing/rest/search/v1/custom/-home-?contentType=ALL&property=cm:name&value=20200422&maxItems=40&skipCount=0
    //https://[EDUSHARINGDOMAIN]/edu-sharing/rest/search/v1/custom/-home-?contentType=ALL&property=cm:name&value=20200422&maxItems=10&skipCount=0
    public function getSearchCustom($repository, $params)
    {
        $ret = $this->call($this->repoUrl.'/rest/search/v1/custom/'.$repository.'?'.http_build_query($params));

        return json_decode($ret, true);
    }

    public function getSearchQueriesV2($repository, $params)
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
        if ($filter != '') {
            $postFields = [
                'criterias' => [
                    [
                        'property' => $params['property'],
                        'values' => [$params['value']],
                    ],
                    [
                        'property' => $filter,
                        'values' => [
                            $filter_value,
                        ],
                    ],
                ],
            ];
        } else {
            $postFields = [
                'criterias' => [
                    [
                        'property' => $params['property'],
                        'values' => [$params['value']],
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
        //dump($this->repoUrl . '/rest/search/v1/queriesV2/' . $repository.'/-default-/curriculum?'.http_build_query($params));
        $ret = $this->call($this->repoUrl.'/rest/search/v1/queriesV2/'.$repository.'/-default-/curriculum?'.http_build_query($params),
            'POST',
            ['Content-Type: application/json'],
            json_encode($postFields)
        );

        return json_decode($ret, true);
    }

    public function getChildren($repository, $parentId, $params)
    {
        $children = $this->call($this->repoUrl.'/rest/node/v1/nodes/'.$repository.'/'.$parentId.'/children?'.http_build_query($params));

        return json_decode($children, true);
    }

    /****************************************************************************
     *
     */
    public function searchRepository($query)
    {
        $repo = isset($query['repo']) ? $query['repo'] : '-home-';    //e.g.'FILES';
        $contentType = isset($query['contentType']) ? $query['contentType'] : 'ALL';    //e.g.'FILES';
        $property = isset($query['property']) ? $query['property'] : 'cm:name';      //e.g.'ccm:competence_digital2';
        $value = $query['value'];          //e.g.11990503;
        $maxItems = isset($query['maxItems']) ? $query['maxItems'] : 100;             // used for pagination
        $skipCount = isset($query['skipCount']) ? $query['skipCount'] : ($query['page'] * $maxItems);            // used for pagination

        return $this->getSearchCustom($repo, ['contentType' => $contentType, 'property' => $property, 'value' => $value, 'maxItems' => $maxItems, 'skipCount' => $skipCount]);
    }

//    public function getExternalsubscriptions ($model, $id, $files)
//    {
//        $subscriptions = ExternalRepositorySubscriptions::where('subscribable_type', get_class($model))
//                                                ->where('subscribable_id', $model->id)
//                                                ->where('repository', self::PLUGINNAME)->get();
//        $this->setTokens(); //(re)set token
//        foreach ($subscriptions as $subscription)
//        {
//            $es_array = array_merge($es_array, $this->processReference($subscription->value));
//        }
//
//        return $files;
//    }

    public function processReference($arguments)
    {
        parse_str($arguments, $query);

        $apiEndpoint = isset($query['endpoint']) ? $query['endpoint'] : 'node';
        $contentType = isset($query['contentType']) ? $query['contentType'] : 'ALL';    // e.g.'FILES';
        $combineMode = isset($query['combineMode']) ? $query['combineMode'] : 'AND';    // AND / OR
        $property = isset($query['property']) ? $query['property'] : 'cm:name';      // e.g.'ccm:competence_digital2';
        $value = isset($query['value']) ? $query['value'] : $arguments;           // e.g.11990503;
        $maxItems = isset($query['maxItems']) ? $query['maxItems'] : 40;             // used for pagination
        $skipCount = isset($query['skipCount']) ? $query['skipCount'] : 0;            // used for pagination
        $propertyFilter = isset($query['propertyFilter']) ? $query['propertyFilter'] : 'cm:creator';  // get creator uuid
        $filter = isset($query['filter']) ? $query['filter'] : '';  // set filter e.g. cm:creator

        //$nodes        = $this->getSearchCustom('-home-', array ('contentType' =>'FILES', 'property' => 'ccm:competence_digital2', 'value' => '11061007', 'maxItems' => 10));

        switch ($apiEndpoint) {
            case 'getSearchCustom': $nodes = $this->getSearchCustom('-home-', ['contentType' => $contentType, 'combineMode' => $combineMode, 'property' => $property, 'value' => $value, 'maxItems' => $maxItems, 'skipCount' => $skipCount]);
                break;
            case 'getSearchQueriesV2': $nodes = $this->getSearchQueriesV2('-home-', ['contentType' => $contentType, 'combineMode' => $combineMode, 'property' => $property, 'value' => $value, 'maxItems' => $maxItems, 'skipCount' => $skipCount, 'propertyFilter' => $propertyFilter, 'filter' => $filter]);
                break;
            case 'getNodeChildren': $nodes = $this->getChildren('-home-', $value, ['maxItems' => $maxItems, 'skipCount' => $skipCount]);
                break;
            case 'node':            $result = $this->getRendering('-home-', $value);

                                    if (isset($result['node'])) {
                                        $nodes['nodes'][] = $result['node'];
                                    } else {
                                        return;
                                    }
                break;
            default:
                break;
        }
        $collection = collect([]);
        if (! isset($nodes['nodes'])) {
            return $collection; //end early if no data is given
        }

        foreach ($nodes['nodes'] as $node) {
            if ($node['mediatype'] == 'folder') { //todo es muss überlegt werden, ob subfolder geladen werden
                continue;
            }

            $collection->push([
                'value' => isset($node['ref']['id']) ? $node['ref']['id'] : $arguments, //value field in db
                'node_id' => isset($node['ref']['id']) ? $node['ref']['id'] : null,
                'uuid' => isset($node['properties']['cm:creator']) ? $node['properties']['cm:creator'][0] : null,
                'license' => isset($node['license']) ? $node['license'] : null,
                'title' => $this->getReadableTitle($node), //isset($node['title']) ?  $node['title'] : $node['name'],
                'description' => isset($node['description']) ? $node['description'] : '',
                'thumb' => isset($node['preview']['url']) ? $node['preview']['url'].'&ticket='.$this->accessToken : '',
                'iconURL' => isset($node['iconURL']) ? $node['iconURL'] : '',
                'path' => isset($node['ref']['id']) ? $this->repoUrl.'/components/render/'.$node['ref']['id'].'?ticket='.$this->accessToken : '',
            ]);
        }

        return $collection;
    }

    private function getReadableTitle($node)
    {
        $title = isset($node['title']) ? $node['title'] : '';
        if (empty(trim($title))) {
            $title = $node['name'];
        }

        return $title;
    }

    public function store($query)
    {
        return RepositorySubscription::firstOrCreate([
            'subscribable_type' => $query['subscribable_type'],
            'subscribable_id'   => $query['subscribable_id'],
            'repository'        => self::PLUGINNAME,
            'value'             => $query['value'],
            'sharing_level_id'  => isset($query['sharing_level_id']) ? $query['sharing_level_id'] : 1,
            'visibility'        => isset($query['visibility']) ? $query['visibility'] : 1,
            'owner_id'          => auth()->user()->id,
        ]);
    }

    public function destroy($query)
    {
        $subsciption = RepositorySubscription::where('subscribable_type', $query['subscribable_type'])
                                ->where('subscribable_id', $query['subscribable_id'])
                                ->where('repository', self::PLUGINNAME)
                                ->where('value', $query['value'])
                                ->where('sharing_level_id', isset($query['sharing_level_id']) ? $query['sharing_level_id'] : 1)
                                ->where('visibility', isset($query['visibility']) ? $query['visibility'] : 1)
                                ->where('owner_id', auth()->user()->id)
                                ->first();

        if ($subsciption) {
            return ['message' => $subsciption->delete()];
        }
    }
}

//get ticket via soap

// auth_data snippet
function edusharing_get_auth_data()
{
    return [
        ['key' => 'userid', 'value' => auth()->user()->common_name],
        ['key' => 'lastname', 'value' => auth()->user()->firstname],
        ['key' => 'firstname', 'value' => auth()->user()->lastname],
        ['key' => 'email', 'value' => auth()->user()->email],
        ['key' => 'affiliation', 'value' => ''],
        ['key' => 'affiliationname', 'value' => ''],
    ];
}
