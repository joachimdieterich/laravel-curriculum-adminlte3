<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App;

use App\RepositoryPlugin;
use App\RepositorySubscription;

/**
 * Description of plugin
 *
 * @author joachimdieterich
 */
class Edusharing extends RepositoryPlugin
{
    const PLUGINNAME = 'edusharing';
    private $accessToken = '';
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
        return "edu-sharing plugin";
    }
    
    public function __construct() 
    {    
        $this->grant_type       = env('EDUSHARING_GRAND_TYPE', 'password'); 
        $this->client_id        = env('EDUSHARING_CLIENT_ID', 'eduApp');
        $this->client_secret    = env('EDUSHARING_CLIENT_SECRET', 'secret');
        $this->repoUrl          = env('EDUSHARING_REPO_URL', '');
        $this->repoUser         = env('EDUSHARING_REPO_USER', ''); 
        $this->repoPwd          = env('EDUSHARING_REPO_PWD', '');
        $this->proxy            = env('EDUSHARING_REPO_PROXY', false);
        $this->proxy            = env('EDUSHARING_REPO_PROXY_PORT', false);

        if (isset(auth()->user()->id))
        {
            $this->setTokens(); //do not set token here to prevent blankpage if edusharing is offline
        }
    }
        
    private function setTokens() 
    {
        $postFields = 'grant_type=' . $this->grant_type . '&client_id=' . $this->client_id . '&client_secret=' . $this->client_secret . '&username=' . $this->repoUser . '&password=' . $this->repoPwd;
        $raw        = $this->call ( $this->repoUrl . '/oauth2/token', 'POST', array (), $postFields );
        $return     = json_decode ( $raw );
        
        $this->accessToken = $return->access_token;
        return $return;
    }
    
    public function getAbout() 
    {
        $ret = $this->call($this->repoUrl . '/rest/_about');
        return json_decode($ret, true);
    } 

    public function createUser($user) 
    {
        $this->call ( 
            $this->repoUrl . '/rest/iam/v1/people/-home-/' . urlencode ( $user ['username'] ), 
            'POST', 
            array ( 'Content-Type: application/json' ), 
            json_encode ( $user ['profile'] ) 
        );
    }
    
    public function setCredential($user) 
    {
        $this->call ( 
            $this->repoUrl . '/rest/iam/v1/people/-home-/' . urlencode ( $user ['username'] ) . '/credential', 
            'PUT', 
            array ('Content-Type: application/json' ), 
            json_encode ( array ( 'newPassword' => $user ['password'] ) ) 
        );
    }
    
    public function getGroup($group) {
        return json_decode($this->call ( $this->repoUrl . '/rest/iam/v1/groups/-home-/' . urlencode ( $group ) ), true);
    }
    
    public function createGroup($group) {
        $this->call ( 
            $this->repoUrl . '/rest/iam/v1/groups/-home-/' . urlencode ( $group ['groupname'] ), 
            'POST', 
            array ( 'Content-Type: application/json' ), 
            json_encode ( $group ['properties'] ) 
        );
    }
    
    public function addMember($group, $member) {
        $this->call ( $this->repoUrl . '/rest/iam/v1/groups/-home-/' . urlencode ( $group ) . '/members/' . urlencode ( $member ), 'PUT' );
    }
    
    public function loginToScope($userName, $password, $scope = '') {
        $ret = $this->call ( 
            $this->repoUrl . '/authentication/v1/loginToScope', 
            'POST', 
            array ( 'Content-Type: application/json' ), 
            json_encode ( 
                array ( 
                    'userName' => $userName,
                    'password' => $password,
                    'scope'    => $scope
                ) 
            ) 
        );
        return json_decode($ret, true);
    }
    
    public function getUser($username = '-me-') {
        return $this->call ( $this->repoUrl . '/rest/iam/v1/people/-home-/' . urlencode ( $username ) );
    }
    
    public function createIoNode($title, $folderId) {
        $postFields = array (
            array ( 
                'name' => '{http://www.alfresco.org/model/content/1.0}name',
                'values' => array ( $title ) 
            ) 
        );
        return $this->call ( 
            $this->repoUrl . '/rest/node/v1/nodes/-home-/' . urlencode ( $folderId ) . '/children?type=%7Bhttp%3A%2F%2Fwww.campuscontent.de%2Fmodel%2F1.0%7Dio', 
            'POST', 
            array ( 'Content-Type: application/json' ), 
            json_encode ( $postFields ) 
        );
    }
    
    public function addNodeContent($nodeId, $versionComment, $mimetype, $postFields) {
        return $this->call (
            $this->repoUrl . '/rest/node/v1/nodes/-home-/' . urlencode ( $nodeId ) . '/content?versionComment=' . urlencode ( $versionComment ) . '&mimetype=' . urlencode ( $mimetype ), 
            'POST',
            array ('Content-Type: multipart/form-data'), 
            $postFields  
        );
    }
    
    public function setPermissions($nodeId, $permissions) {
        $postFields = array (
            'inherited' => false,
            'permissions' => $permissions 
        );
        $this->call ( 
            $this->repoUrl . '/rest/node/v1/nodes/-home-/' . $nodeId . '/permissions', 
            'PUT', array ('Content-Type: application/json' ), 
            json_encode ( $postFields )
        );
    }

    public function createFolder($title, $parentId) {
        $postFields = array (
            array (
                'name' => '{http://www.alfresco.org/model/content/1.0}name',
                'values' => array ( $title )
            )
        );
        $node = $this->call (
            $this->repoUrl . '/rest/node/v1/nodes/-home-/' . urlencode ( $parentId ) . '/children?type=' . urlencode('{http://www.campuscontent.de/model/1.0}map'), 
            'POST', 
            array ( 'Content-Type: application/json' ), 
                json_encode ( $postFields ) 
             );
        $return = json_decode ( $node );
        return $return->node->ref->id;
    }

    public function setOrganization($organization, $folderId) {
        $this->call(
            $this->repoUrl . '/rest/organization/v1/organizations/-home-/'.$organization . '?folder=' . $folderId, 
            'PUT', 
            array(), 
            json_encode(array('organization'=>$organization, 'folder' => $folderId)) 
        );
    }

    public function getCompanyHome() {
        $return = json_decode($this->call($this->repoUrl . '/rest/node/v1/nodes/-home-?query=' . urlencode('PATH:"/app:company_home"')), true);
        return $return['nodes'][0]['ref']['id'];
    }
    
    public function searchNodes($repository, $params) {
        $return = $this->call($this->repoUrl . '/rest/node/v1/nodes/' . $repository.'?'.http_build_query($params));
        return json_decode($return, true);
    }

    public function getPerson($person = '-me-') {
        $return = $this->call($this->repoUrl . '/rest/iam/v1/people/-home-/' . $person);
        return json_decode($return, true);
    }

    public function getAllGroups() {
        $ret = $this->call($this->repoUrl . '/rest/iam/v1/groups/-home-?pattern=*');
        return json_decode($ret, true);
    }

    public function getOrganizations() {
        $ret = $this->call($this->repoUrl . '/rest/organization/v1/organizations/-home-');
        return json_decode($ret, true);
    }

    private function call($url, $httpMethod = '', $additionalHeaders = array(), $postFields = array()) {      
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 5);  //timeout in seconds
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10); //timeout in seconds

        if ($this->proxy){
            curl_setopt ( $ch, CURLOPT_PROXY, $this->proxy);
            if ($this->proxy_port){
                curl_setopt ( $ch, CURLOPT_PROXYPORT, $this->proxy_port);
            }
        }

        switch ($httpMethod) {
            case 'POST' :
                    curl_setopt ( $ch, CURLOPT_POST, true );
                    break;
            case 'PUT' :
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    break;
            case 'DELETE' :
                    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" );
                    break;
            default : break;
        }

        $headers = array_merge ( array (
                        'Accept: application/json',
                        'Authorization: Bearer ' . $this->accessToken 
        ), $additionalHeaders );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );

        if (! empty ( $postFields )) {
                curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
        }

        $exec = curl_exec ( $ch );

        if ($exec === false) {
                 error_log($url . ' ---> ' .  curl_error ( $ch ) .' ---> Error-Code:' .curl_errno($ch)); // for debugging
                //throw new Exception ( curl_error ( $ch ) );
        }

        $httpcode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
        if ($httpcode != 200) {
                //deal with it
        }
        curl_close ( $ch );
        return $exec;
    }

    public function getCollections($repository, $collection) {
        $getFields = array (
                        'repository' => $repository,
                        'collection' => $collection 
        );
        $ret =$this->call ( 
            $this->repoUrl . '/rest/collection/v1/collection/' . $repository . '/permissions', 
            'GET', 
            array ('Content-Type: application/json' ), 
            json_encode ( $getFields ) 
        );
        return json_decode($ret, true);
    }

    public function getRendering($repository, $node) {
        $ret = $this->call($this->repoUrl . '/rest/rendering/v1/details/' . $repository . '/' . $node);
        return json_decode($ret, true);
    }

    //https://[EDUSHARINGDOMAIN]/edu-sharing/swagger/#!/SEARCH_v1/searchByProperty
    public function getSearchCustom($repository, $params) {
        $ret =$this->call ( $this->repoUrl . '/rest/search/v1/custom/' . $repository.'?'.http_build_query($params));
        return json_decode($ret, true);
    }

    public function getChildren($repository, $parentId, $params) {
        $children = $this->call($this->repoUrl . '/rest/node/v1/nodes/'. $repository.'/'.$parentId.'/children?'.http_build_query($params));
        return json_decode($children, true);
    }

    /****************************************************************************
     * 
     */
    public function searchRepository($query)
    {
      
        $repo    = isset($query['repo']) ? $query['repo'] : '-home-';    //e.g.'FILES';
        $contentType    = isset($query['contentType']) ? $query['contentType'] : 'ALL';    //e.g.'FILES';
        $property       = isset($query['property']) ? $query['property'] : "cm:name";      //e.g.'ccm:competence_digital2';
        $value          = $query['value'];          //e.g.11990503;
        $maxItems       = 40;
        $skipCount      = 0;
        return $this->getSearchCustom($repo, array ('contentType' =>$contentType, 'property' => $property, 'value' => $value, 'maxItems' => $maxItems, 'skipCount' => $skipCount));
    }
    
    public function getExternalsubscriptions ($model, $id, $files){
        $subscriptions = ExternalRepositorySubscriptions::where('subscribable_type', get_class($model))
                                                ->where('subscribable_id', $model->id)
                                                ->where('repository', self::PLUGINNAME)->get();
        $this->setTokens(); //(re)set token
        foreach ($subscriptions as $subscription) {
            $es_array = array_merge($es_array, $this->processReference($subscription->value));
        }

        return $files;
    }

    public function processReference($arguments){
        parse_str($arguments, $query);

        $apiEndpoint    = isset($query['endpoint']) ?  $query['endpoint'] : 'node';             
        $contentType    = isset($query['contentType']) ? $query['contentType'] : null;    //e.g.'FILES';
        $property       = isset($query['property']) ? $query['property'] : null;      //e.g.'ccm:competence_digital2';
        $value          = isset($query['value']) ? $query['value'] : $arguments;          //e.g.11990503;
        $maxItems       = 40;
        $skipCount      = 0;

        //$nodes        = $this->getSearchCustom('-home-', array ('contentType' =>'FILES', 'property' => 'ccm:competence_digital2', 'value' => '11061007', 'maxItems' => 10));
        switch ($apiEndpoint) {
            case 'getSearchCustom': $nodes      = $this->getSearchCustom('-home-', array ('contentType' =>$contentType, 'property' => $property, 'value' => $value, 'maxItems' => $maxItems, 'skipCount' => $skipCount));
                break;
            case 'getNodeChildren': $nodes      = $this->getChildren('-home-', $value, array ('maxItems' => $maxItems, 'skipCount' => $skipCount));
                break;
            case 'node':            $result = $this->getRendering('-home-', $value);
                                    if (isset($result['node'])){
                                        $nodes['nodes'][] =$result['node'] ;
                                    } else {
                                        return;
                                    }
                break;
            default:
                break;
        }

        $collection = collect([]);
        
        foreach ($nodes['nodes'] as $node) {
            if ($node['mediatype'] == 'folder'){ //todo es muss überlegt werden, ob subfolder geladen werden
                continue;
            }
            $collection->push([
                'value'       => $arguments, //value field in db
                'node_id'     => $node['ref']['id'],
                'license'     => $node['licenseURL'],
                'title'       => isset($node['title']) ?  $node['title'] : $node['name'],
                'description' => $node['description'],
                'thumb'       => $node['preview']['url'],
                'path'        => $this->repoUrl . '/components/render/' .$node['ref']['id']
            ]);
        }

        return $collection;
    }


    public function store($query){
        
        return RepositorySubscription::firstOrCreate([
            'subscribable_type' => $query['subscribable_type'],
            'subscribable_id'   => $query['subscribable_id'],
            'repository'        => self::PLUGINNAME,
            'value'             => $query['value'],
            'sharing_level_id'  => isset($query['sharing_level_id']) ?  $query['sharing_level_id'] : 1,
            'visibility'        => isset($query['visibility']) ?  $query['visibility'] : 1,
            'owner_id'          => auth()->user()->id,
        ]);
        
    }
    
    public function destroy($query){
        $subsciption =  RepositorySubscription::where('subscribable_type', $query['subscribable_type'])
                                ->where('subscribable_id', $query['subscribable_id'])
                                ->where('repository', self::PLUGINNAME)
                                ->where('value', $query['value'])
                                ->where('sharing_level_id', isset($query['sharing_level_id']) ?  $query['sharing_level_id'] : 1)
                                ->where('visibility', isset($query['visibility']) ?  $query['visibility'] : 1)
                                ->where('owner_id', auth()->user()->id)
                                ->first();
        
        if ($subsciption)
        {
            return ['message' => $subsciption->delete()];
        }
        
    }
}
