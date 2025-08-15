<?php

namespace App\Plugins\Repositories\edusharing;

use App\Plugins\Repositories\RepositoryPlugin;
use App\RepositorySubscription;
use App\User;

/**
 * Description of plugin
 *
 * @author joachimdieterich
 */
class Edusharing extends RepositoryPlugin
{
    const PLUGINNAME = 'edusharing';

    public $accessToken = null;

    private $repoUrl;

    private $proxy = false;

    private $proxy_port = false;

    public function about()
    {
        return 'edu-sharing plugin';
    }

    public function __construct()
    {
        $this->repoUrl = env('EDUSHARING_REPO_URL', '');
        $this->proxy = env('EDUSHARING_REPO_PROXY', false);
        $this->proxy_port = env('EDUSHARING_REPO_PROXY_PORT', false);
    }

    function helperBase($owner_id = null)
    {
        $base = new EduSharingHelperBase(
            env('EDUSHARING_REPO_URL', ''),
            env('EDUSHARING_PRIV_KEY', ''),
            env('EDUSHARING_APP_ID', ''),
        );

        $authHelper = new EduSharingAuthHelper($base);

        if ($owner_id == null)
        {
            $common_name = auth()->user()->common_name; //get ticket for current user
        }
        else
        {
            $common_name =  User::where('id', $owner_id)->get()->first()->common_name; //get ticket for $owner_id
        }

        if (!is_guest()) {
            $ticket = $authHelper->getTicketForUser($common_name);
            $this->accessToken = $ticket;
        }

        return $base;
    }

    public function createUsage(
        string $subscribable_type,
        string $subscribable_id,
        string $nodeId,
        string $nodeVersion = null
    )
    {
        $nodeHelper = new EduSharingNodeHelper($this->helperBase());
        return $nodeHelper->createUsage(
            $this->accessToken,
            $subscribable_type, //course_id
            $subscribable_id, //resourceId
            $nodeId, //$nodeId
            $nodeVersion
        );
    }

    public function deleteUsage(
        string $nodeId,
        string $usageId
    )
    {
        $nodeHelper = new EduSharingNodeHelper($this->helperBase());
        return $nodeHelper->deleteUsage(
            $nodeId,
            $usageId
        );
    }

    public function getNodeByUsage($usage, $owner_id)
    {
        $nodeHelper = new EduSharingNodeHelper($this->helperBase($owner_id));

        return $nodeHelper->getNodeByUsage(
            new Usage(
                $usage['nodeId'],
                $usage['nodeVersion'],
                $usage['containerId'],
                $usage['resourceId'],
                $usage['usageId'],
            )
        );
    }

    public function getRedirectUrl($usage, $mode, $owner_id)
    {
        $nodeHelper = new EduSharingNodeHelper($this->helperBase($owner_id));

        return $nodeHelper->getRedirectUrl(
            $mode,
            new Usage(
                $usage['nodeId'],
                $usage['nodeVersion'],
                $usage['containerId'],
                $usage['resourceId'],
                $usage['usageId'],
            )
        );
    }

    public function getPreview($usage, $owner_id)
    {
        $nodeHelper = new EduSharingNodeHelper($this->helperBase($owner_id));

        return $nodeHelper->getPreview(
            new Usage(
                $usage['nodeId'],
                $usage['nodeVersion'] ?? null,
                $usage['containerId'],
                $usage['resourceId'],
                $usage['usageId'],
            )
        );
    }

    public function getRendering($repository, $node)
    {
        $apiHelper = new EduSharingApiHelper($this->helperBase());

        return $apiHelper->getRendering(
            $this->accessToken,
            $repository,
            $node
        );
    }

    public function getSearchCustom($repository, $params)
    {
        $apiHelper = new EduSharingApiHelper($this->helperBase());

        return $apiHelper->getSearchCustom(
            $this->accessToken,
            $repository,
            $params
        );
    }

    public function getSearchQueriesV2($repository, $params)
    {
        $apiHelper = new EduSharingApiHelper($this->helperBase());

        return $apiHelper->getSearchQueriesV2(
            $this->accessToken,
            $repository,
            $params
        );
    }

    public function getChildren($repository, $parentId, $params)
    {
        $apiHelper = new EduSharingApiHelper($this->helperBase());

        return $apiHelper->getChildren(
            $this->accessToken,
            $repository,
            $parentId,
            $params
        );
    }

    /****************************************************************************
     *
     */
    public function searchRepository($query)
    {
        $repo = $query['repo'] ?? '-home-';                 //e.g.'FILES';
        $contentType = $query['contentType'] ?? 'ALL';      //e.g.'FILES';
        $property = $query['property'] ?? 'cm:name';        //e.g.'ccm:competence_digital2';
        $value = $query['value'];                           //e.g.11990503;
        $maxItems = $query['maxItems'] ?? 100;              // used for pagination
        $skipCount = $query['skipCount'] ?? ($query['page'] * $maxItems);            // used for pagination

        return $this->getSearchCustom(
            $repo,
            [
                'contentType' => $contentType,
                'property' => $property,
                'value' => $value,
                'maxItems' => $maxItems,
                'skipCount' => $skipCount
            ]
        );
    }

    public function processReference($arguments)
    {
        parse_str($arguments, $query);

        $apiEndpoint = $query['endpoint'] ?? 'node';
        $contentType = $query['contentType'] ?? 'ALL';    // e.g.'FILES';
        $combineMode = $query['combineMode'] ?? 'AND';    // AND / OR
        $property = $query['property'] ?? 'cm:name';      // e.g.'ccm:competence_digital2';
        $value = $query['value'] ?? $arguments;           // e.g.11990503;
        $maxItems = $query['maxItems'] ?? 40;             // used for pagination
        $skipCount = $query['skipCount'] ?? 0;            // used for pagination
        $propertyFilter = $query['propertyFilter'] ?? 'cm:creator';  // get creator uuid
        $filter = $query['filter'] ?? '';  // set filter e.g. cm:creator

        switch ($apiEndpoint) {
            case 'getSearchCustom': $nodes = $this->getSearchCustom(
                        '-home-',
                        [
                            'contentType' => $contentType,
                            'combineMode' => $combineMode,
                            'property' => $property,
                            'value' => $value,
                            'maxItems' => $maxItems,
                            'skipCount' => $skipCount
                        ]
                    );
                break;
            case 'getSearchQueriesV2': $nodes = $this->getSearchQueriesV2(
                        '-home-',
                        [
                            'contentType' => $contentType,
                            'combineMode' => $combineMode,
                            'property' => $property,
                            'value' => $value,
                            'maxItems' => $maxItems,
                            'skipCount' => $skipCount,
                            'propertyFilter' => $propertyFilter,
                            'filter' => $filter
                        ]
                    );
                break;
            case 'getNodeChildren': $nodes = $this->getChildren(
                        '-home-',
                        $value,
                        [
                            'maxItems' => $maxItems,
                            'skipCount' => $skipCount
                        ]
                    );
                break;
            case 'node':  $result = $this->getRendering('-home-', $value);

                        if (isset($result['node']))
                        {
                            $nodes['nodes'][] = $result['node'];
                        }
                        else
                        {
                            return;
                        }
                break;
            default:
                break;
        }
        $collection = collect([]);
        if (! isset($nodes['nodes']))
        {
            return $collection; //end early if no data is given
        }

        foreach ($nodes['nodes'] as $node)
        {
            if ($node['mediatype'] == 'folder')
            { //todo: load subfolder?
                continue;
            }

            $collection->push([
                'value' => $node['ref']['id'] ?? $arguments, //value field in db
                'node_id' => $node['ref']['id'] ?? null,
                'uuid' => isset($node['properties']['cm:creator']) ? $node['properties']['cm:creator'][0] : null,
                'license' => $node['license'] ?? null,
                'title' => $this->getReadableTitle($node),
                'description' => $node['description'] ?? '',
                'thumb' => isset($node['preview']['url']) ? $node['preview']['url'].'&ticket='.$this->accessToken : '',
                'iconURL' => $node['iconURL'] ?? '',
                'path' => isset($node['ref']['id']) ? $this->repoUrl.'/components/render/'.$node['ref']['id'].'?ticket='.$this->accessToken : '',
            ]);
        }

        return $collection;
    }

    private function getReadableTitle($node)
    {
        $title = $node['title'] ?? '';
        if (empty(trim($title)))
        {
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

        if ($subsciption)
        {
            return ['message' => $subsciption->delete()];
        }
    }
}
