<?php

namespace App\Plugins\Repositories\edusharing;

use App\Http\Controllers\LogController;
use App\Interfaces\MediaInterface;
use App\Medium;
use App\MediumSubscription;
use Illuminate\Http\Request;

class EdusharingMediaAdapter implements MediaInterface
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $edusharing = new Edusharing;

        return $edusharing->getSearchQueriesV2('-home-',$request);

    }

    public function list()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $base = new EduSharingHelperBase(
            env('EDUSHARING_REPO_URL', ''),
            env('EDUSHARING_PRIV_KEY', ''),
            env('EDUSHARING_APP_ID', ''),
        );

        $authHelper = new EduSharingAuthHelper($base);

        $ticket = $authHelper->getTicketForUser(auth()->user()->common_name);

        if (request()->wantsJson())
        {
            return [
                'uploadIframeUrl' => config('medium.repositories.edusharing.upload_iframe_url').'&ticket='.$ticket,
                'cloudIframeUrl' => config('medium.repositories.edusharing.cloud_iframe_url').'&ticket='.$ticket,
            ];
        }
        abort(404);
    }

    public function store(Request $request)
    {
        $input = $this->validateRequest();

        $medium = Medium::create([
            'adapter'       => $input['repository'],
            'external_id'   => $input['external_id'],
            'path'          => $input['path'],
            'thumb_path'    => $input['thumb_path'],
            'medium_name'   => $input['medium_name']    ?? '',
            'title'         => $input['title']          ?? '',
            'description'   => $input['description']    ?? '',
            'author'        => $input['author']         ?? '',
            'publisher'     => $input['publisher']      ?? '',
            'city'          => $input['city']           ?? '',
            'date'          => date('Y-m-d_H-i-s'),
            'size'          => $input['size']           ?? 0,
            'mime_type'     => $input['mime_type']      ?? 'edusharing',
            'license_id'    => $input['license_id']     ?? 1,
            'public'        => $input['public']         ?? 1,   //default is public --> permission check over edusharing

            'owner_id' => auth()->user()->id,
        ]);

        if (($input['subscribable_type'] !== 'null') and ($input['subscribable_id'] !== 'null'))
        {
            //create usage
            try {
                $edusharing = new Edusharing;

                $usage = $edusharing
                    ->createUsage(
                        $input['subscribable_type'],
                        $input['subscribable_id'],
                        $input['external_id'],
                    );
            }
            catch (Exception $e)
            {
                dump($e->getMessage());
            }


            //subscribe
            $subscribe = MediumSubscription::updateOrCreate(
                [
                    'medium_id'         => $medium->id,
                    'subscribable_type' => $input['subscribable_type'],
                    'subscribable_id'   => $input['subscribable_id'],
                ],
                [
                    'sharing_level_id'  => 1,
                    'visibility'        => 1,
                    'additional_data'   => $usage ?? '',
                    'owner_id'          => auth()->user()->id,
                ]);
            $subscribe->save();
        }

        LogController::set(get_class($this).'@'.__FUNCTION__, null, 1);

        return response()->json($medium);
    }

    public function show(Medium $medium)
    {
        // Medium is public (sharing_level_id == 1) or user is owner
        /*if (($medium->public == true) or ($medium->owner_id == auth()->user()->id)) {
            return request('download') ? redirect($medium->path) : redirect($medium->thumb_path);
        }*/

        /* checkIfUserHasSubscription and visibility*/
        if ($medium->subscriptions())
        {
            foreach ($medium->subscriptions as $subscription)
            {
                if ($this->checkIfUserHasSubscription($subscription))
                {
                    if ($subscription->additional_data != null)
                    {
                        $edusharing = new Edusharing;
                        $node = $edusharing->getNodeByUsage($subscription->additional_data, $subscription->owner_id);

                        if (request()->wantsJson())
                        {
                            return [
                                'detailsSnippet' => $node['detailsSnippet']
                            ];
                        }
                        //return request('download') ? redirect($node['node']['downloadUrl']['url']) : redirect($node['node']['preview']['url']);
                    }

                    //return request('download') ? redirect($medium->path) : redirect($medium->thumb_path);
                }
            }
        }
        /* end checkIfUserHasSubscription and visibility */
        abort(403); // user has no permission to access this file !
    }

    public function thumb(Medium $medium, $size) //todo: return smaller images/files/thumbs
    {

        // Medium is public (sharing_level_id == 1) or user is owner
        if (($medium->public == true) or ($medium->owner_id == auth()->user()->id))
        {
            return redirect($medium->thumb_path);
        }

        /* checkIfUserHasSubscription and visibility*/
        if ($medium->subscriptions())
        {
            foreach ($medium->subscriptions as $subscription)
            {
                if ($this->checkIfUserHasSubscription($subscription))
                {
                    return redirect($medium->thumb_path); //return file or url
                }
            }
        }
        /* end checkIfUserHasSubscription and visibility */

        abort(403); // user has no permission to access this file !
    }

    public function edit(Medium $medium)
    {
        abort(404);
    }

    public function update(Request $request, Medium $medium)
    {
        abort_unless(\Gate::allows('medium_edit'), 403);

        if ($medium->owner_id === auth()->user()->id)
        {
            $medium->update($this->validateRequest());

            return response()->json(['message' => $medium]);
        }
        else
        {
            return response()->json(['errors' => 'Only file-owner can edit'], 403);
        }
    }

    public function destroy(Medium $medium, mixed $subscribable_type, mixed $subscribable_id)
    {
        abort_unless(\Gate::allows('medium_delete'), 403);
        /**
         * check if medium is subscribed only by deleting reference
         * - if yes -> delete medium_subscription and medium
         * - if not -> delete only medium_subscription
         */
        $input = $this->validateRequest();
        if (isset($input['subscribable_type']) and isset($input['subscribable_id']))
        {
            $subscribable_type = $input['subscribable_type'];
            $subscribable_id = $input['subscribable_id'];

            $subscription = MediumSubscription::where([
                ['subscribable_type', $subscribable_type],
                ['subscribable_id', $subscribable_id],
                ['medium_id', $medium->id],
            ])->first()->get();

            // delete usage
            $edusharing = new Edusharing;
            $edusharing->deleteUsage(
                $subscription->additional_data['node_id'],
                $subscription->additional_data['usage_id'],
            );

            $subscription->delete();
        }

        if ($medium->subscriptions()->count() <= 1)
        {
            $medium->delete();
        }

        if (request()->wantsJson())
        {
            return ['message' => true];
        }
    }

    public function checkIfUserHasSubscription($subscription)
    {
        switch ($subscription->subscribable_type) {
            case "App\Organization":
                if (in_array($subscription->subscribable_id, auth()->user()->organizations()->pluck('organization_id')->toArray())
                    and ($subscription->visibility == 1))
                {
                    return true;
                }
                break;
            case "App\Group":
                if (in_array($subscription->subscribable_id, auth()->user()->groups()->pluck('groups.id')->toArray())
                    and ($subscription->visibility == 1))
                {
                    return true;
                }
                break;
            case "App\User":
                if ($subscription->subscribable_id == auth()->user()->id
                    and ($subscription->visibility == 1))
                {
                    return true;
                }
                break;
            case "App\KanbanItem":
                if ($subscription->subscribable->kanban->isAccessible())
                {
                    return true;
                }
                break;
            case "App\Kanban":
            case "App\Logbook":
                    return $subscription->subscribable->isAccessible();
                break;

            default: return false;
                break;
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'path' => 'sometimes',
            'thumb_path' => 'sometimes',
            'external_id' => 'sometimes',
            'adapter' => 'sometimes',
            'subscribable_type' => 'sometimes',
            'subscribable_id' => 'sometimes',
            'repository' => 'sometimes',
            'artefact' => 'sometimes',
            'file.*' => 'sometimes|mimes:jpg,jpeg,png,gif,bmp,tiff,tif,ico,svg,mov,mp4,m4v,mpeg,mpg,mp3,m4a,m4b,wav,mid,avi,ppt,pps,pptx,doc,docx,pdf,xls,xlsx,xps,odt,odp,ods,odg,odc,odb,odf,key,numbers,pages,csv,txt,rtx,rtf,zip,psd,xcf',

            'title' => 'sometimes',
            'size' => 'sometimes',
            'mime_type' => 'sometimes',
            'description' => 'sometimes',
            'author' => 'sometimes',
            'publisher' => 'sometimes',
            'city' => 'sometimes',
            'license_id' => 'sometimes',
            'public' => 'sometimes',
        ]);
    }
}
