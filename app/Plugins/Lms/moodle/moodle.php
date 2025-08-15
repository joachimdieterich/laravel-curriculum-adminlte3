<?php

namespace App\Plugins\Lms\moodle;

use App\LmsReference;
use App\LmsUserToken;
use App\Organization;
use App\Plugins\Lms\LmsPlugin;

/**
 * Description of plugin
 *
 * @author joachimdieterich
 */
class moodle extends LmsPlugin
{
    const PLUGINNAME = 'moodle';

    private $lmsUrl;

    private $wsPath;

    private $wsToken;

    private $queryParams;

    private $proxy = false;

    private $proxy_port = false;

    private $headerArray = [];

    public function about()
    {
        return 'moodle plugin';
    }

    public function __construct($service = 'moodle_mobile_app', $passport = '12345', $urlscheme = 'moodledownloader', $sso = true)
    {
        $this->lmsUrl = Organization::find(auth()->user()->current_organization_id)->lms_url;
        $this->wsPath = 'webservice/rest/server.php?';
        $this->wsToken = optional(
            LmsUserToken::where([
                'organization_id' => auth()->user()->current_organization_id,
                'user_id' => auth()->user()->id,
            ])->get()->first()
        )->token;
        $this->queryParams = [
            'wstoken' => $this->wsToken,
            'moodlewsrestformat' => 'json',
        ];
    }

    public function store($query)
    {
        return LmsReference::firstOrCreate([
            'referenceable_type' => $query['referenceable_type'],
            'referenceable_id' => $query['referenceable_id'],
            'repository' => self::PLUGINNAME,
            'value' => [
                'course_id' => $query['course_id'],
                'course_content_id' => $query['course_content_id'],
                'course_item' => $query['course_item'],
            ],
            'sharing_level_id' => isset($query['sharing_level_id']) ? $query['sharing_level_id'] : 4,
            'visibility' => isset($query['visibility']) ? $query['visibility'] : 1,
            'owner_id' => auth()->user()->id,
        ]);
    }

    public function show($query)
    {
        $userCanSee = auth()->user()->lmsReferences()->where([
            'referenceable_type' => $query['referenceable_type'],
            'referenceable_id' => $query['referenceable_id'],
        ])->get();

        foreach (auth()->user()->currentGroups as $group) {
            $userCanSee = $userCanSee->merge($group->lmsReferences()->where([
                'referenceable_type' => $query['referenceable_type'],
                'referenceable_id' => $query['referenceable_id'],
            ])->get());
        }

        $organization = Organization::find(auth()->user()->current_organization_id)->lmsReferences()->where([
            'referenceable_type' => $query['referenceable_type'],
            'referenceable_id' => $query['referenceable_id'],
        ])->get();
        $userCanSee = $userCanSee->merge($organization);

        $ownedByUser = LmsReference::where([
            'referenceable_type' => $query['referenceable_type'],
            'referenceable_id' => $query['referenceable_id'],
            'owner_id' => auth()->user()->id,
        ])->get();
        $userCanSee = $userCanSee->merge($ownedByUser);

        return $userCanSee->unique();
    }

    public function core_course_get_courses_by_field()
    {
        $params = array_merge(
            $this->queryParams,
            [
                'wsfunction' => 'core_course_get_courses_by_field',
            ]
        );

        return json_decode($this->call($params));
    }

    public function core_course_get_courses()
    {
        $params = array_merge(
            $this->queryParams,
            [
                'wsfunction' => 'core_course_get_courses',
            ]
        );

        return json_decode($this->call($params));
    }

    public function core_course_get_contents($params)
    {
        $params = array_merge(
            $this->queryParams,
            [
                'wsfunction' => 'core_course_get_contents',
                'courseid' => $params['course_id'],
            ]
        );

        return json_decode($this->call($params));
    }

    private function call($params, $httpMethod = '', $additionalHeaders = [], $postFields = [])
    {
        $url = $this->lmsUrl.$this->wsPath.http_build_query($params); //build url

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  //timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

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
            default:
                break;
        }

        $headers = array_merge([
            'Accept: application/json',
        ], $additionalHeaders);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (! empty($postFields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        }

        $exec = curl_exec($ch);

        // Return headers seperatly from the Response Body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($exec, 0, $header_size);
        $body = substr($exec, $header_size);

        if ($exec === false) {
            error_log($url.' ---> '.curl_error($ch).' ---> Error-Code:'.curl_errno($ch)); // for debugging
            //throw new Exception ( curl_error ( $ch ) );
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode != 200) {
        }

        curl_close($ch);

        $this->headerArray = [];

        foreach (explode("\r\n", $headers) as $value) {
            $matches = explode(':', $value, 2);
            if (isset($matches[1])) {
                $this->headerArray["{$matches[0]}"] = trim($matches[1]);
            }
        }

        return $body;
    }
}
