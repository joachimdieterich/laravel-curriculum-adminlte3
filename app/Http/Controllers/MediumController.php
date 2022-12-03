<?php

namespace App\Http\Controllers;

use App\Medium;
use App\MediumSubscription;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\DataTables;

class MediumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('medium_access'), 403);

        if (request()->wantsJson()) {
            return Medium::where('owner_id', auth()->user()->id)->orderBy('created_at', 'DESC')->paginate($request->input('per_page'));
        }

        return view('media.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('medium_access'), 403);
        $media = (auth()->user()->role()->id == 1) ? Medium::all() : auth()->user()->media()->get();

        $delete_gate = \Gate::allows('medium_delete');

        return DataTables::of($media)
            ->addColumn('action', function ($media) use ($delete_gate) {
                $actions = '';
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                            .'class="btn text-danger" '
                            .'onclick="destroyDataTableEntry(\'media\','.$media->id.')">'
                            .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $input = $this->validateRequest();

            $files = $request->file('file');
            $uploaded = new Collection();
            $pathPrefix = '/users/'.auth()->user()->id.'/';
            foreach ($files as $file) {
                $filename = time().'_'.pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.'.$file->getClientOriginalExtension(); //todo: filename should be editable

                if ($file->storeAs($pathPrefix.$input['path'], $filename, config('filesystems.default'))) {
                    $uploaded->push($this->onStore($file, $filename, $input));
                    if (($input['subscribable_type'] !== 'null') and ($input['subscribable_id'] !== 'null')) {
                        $this->subscribe($uploaded->last(), $input['subscribable_type'], $input['subscribable_id']);
                    }
                }
            }

            LogController::set(get_class($this).'@'.__FUNCTION__, null, (is_array($files)) ? count($files) : 1);

            return response()->json($uploaded->all());
        }
    }

    public function onStore($file, $filename, $input)
    {
        $pathPrefix = '/users/'.auth()->user()->id.'/';

        return Medium::create([
            'path'          => $pathPrefix.(($input['path'] == '') ? '' : $input['path'].'/'),
            'medium_name'   => $filename,
            'title'         => (isset($input['title']) ? $input['title'] : $file->getClientOriginalName()),
            'description'   => (isset($input['description']) ? $input['description'] : ''),
            'author'        => auth()->user()->username,
            'publisher'     => (isset($input['publisher']) ? $input['publisher'] : ''),
            'city'          => (isset($input['city']) ? $input['city'] : ''),
            'date'          => date('Y-m-d_H-i-s'),
            'size'          => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'license_id'    => (isset($input['license_id']) ? $input['license_id'] : 2),
            'public'        => (isset($input['public']) ? $input['public'] : 0),   //default not public

            'owner_id'      => auth()->user()->id,
        ]);
    }

    public function subscribe($medium, $subscribable_type, $subscribable_id, $sharing_level_id = 1, $visibility = 1)
    {
        $subscribe = MediumSubscription::updateOrCreate([
            'medium_id'         => $medium->id,
            'subscribable_type' => $subscribable_type,
            'subscribable_id'   => $subscribable_id,
        ], [
            'sharing_level_id'  => $sharing_level_id,
            'visibility'        => $visibility,
            'owner_id'          => auth()->user()->id,
        ]);
        $subscribe->save();

        return $subscribe;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function show(Medium $medium)
    {
        /* id link */
        if (($medium->mime_type != 'url')) {
            $path = storage_path('app'.$medium->path.$medium->medium_name);
            //dd($path);
            if (! file_exists($path)) {
                abort(404);
            }
        }

        /*
         * Medium is public (sharing_level_id == 1) or user is owner
         */
        if (($medium->public == true) or ($medium->owner_id == auth()->user()->id)) {
            return ($medium->mime_type != 'url') ? response()->file($path, ['Content-Disposition' => 'filename="'.$medium->medium_name.'"']) : redirect($medium->path); //return file or url
        }

        /* checkIfUserHasSubscription and visibility*/
        if ($medium->subscriptions()) {
            foreach ($medium->subscriptions as $subscription) {
                if ($this->checkIfUserHasSubscription($subscription)) {
                    return ($medium->mime_type != 'url') ? response()->file($path) : redirect($medium->path); //return file or url
                }
            }
        }
        /* end checkIfUserHasSubscription and visibility */

        /* user has permission to access this file ! */
        abort(403);
    }

    public function thumb(Medium $medium, $size = 200) //todo: return smaller images/files/thumbs
    {
        /* id link */
        if (($medium->mime_type != 'url')) {
            $path = storage_path('app'.$medium->path.$medium->medium_name);
            $thumb_path = storage_path('app'.$medium->path.'th_'.$size.'_'.$medium->medium_name);

            if (! file_exists($path)) {
                abort(404);
            }
            if (! file_exists($thumb_path)) {
                $img = Image::make($path)->resize($size, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                // save file as jpg with medium quality
                $img->save($thumb_path, 60);
            } else {
                $img = Image::make($thumb_path);
            }
        }
        /*
         * Medium is public (sharing_level_id == 1) or user is owner
         */
        if (($medium->public == true) or ($medium->owner_id == auth()->user()->id)) {
            return ($medium->mime_type != 'url') ? $img->response('jpg') : redirect($medium->path); //return file or url
        }

        /* checkIfUserHasSubscription and visibility*/
        if ($medium->subscriptions()) {
            foreach ($medium->subscriptions as $subscription) {
                if ($this->checkIfUserHasSubscription($subscription)) {
                    return ($medium->mime_type != 'url') ? $img->response('jpg') : redirect($medium->path); //return file or url
                }
            }
        }
        /* end checkIfUserHasSubscription and visibility */

        /* user has permission to access this file ! */
        abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function edit(Medium $medium)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medium $medium)
    {
        abort_unless(\Gate::allows('medium_edit'), 403);

        if ($medium->owner_id === auth()->user()->id) {
            $medium->update($this->validateRequest());

            return response()->json(['message' => $medium]);
        } else {
            return response()->json(['errors' => 'Only file-owner can edit'], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medium $medium, $subscribable_type = null, $subscribable_id = null)
    {
        abort_unless(\Gate::allows('medium_delete'), 403);
        /**
         * check if medium is subscribed only by deleting reference
         * - if yes -> delete medium_subscription and medium
         * - if not -> delete only medium_subscription
         */
        $input = $this->validateRequest();
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $subscribable_type = $input['subscribable_type'];
            $subscribable_id = $input['subscribable_id'];

            MediumSubscription::where([
                ['subscribable_type', $subscribable_type],
                ['subscribable_id', $subscribable_id],
                ['medium_id', $medium->id],
            ])
            ->delete();
        }

        if ($medium->subscriptions()->count() <= 1) {
            Storage::disk(config('filesystems.default'))->delete($medium->path.$medium->medium_name);

            $medium->delete();
        }
        // axios call?
        if (request()->wantsJson()) {
            return ['message' => true];
        }
    }

    public function massDestroy(Medium $medium)
    {
        abort(404);
    }

    public function getMediumByEventPath($path)
    {
        $m = new Medium();

        return Medium::where('path', $m->convertFilemanagerEventPathToMediumPath($path))
                        ->where('medium_name', basename($path))
                        ->get()->first();
    }

    public function checkIfUserHasSubscription($subscription)
    {
        switch ($subscription->subscribable_type) {
            case "App\Organization":
                if (in_array($subscription->subscribable_id, auth()->user()->organizations()->pluck('organization_id')->toArray())
                    and ($subscription->visibility == 1)) {
                    return true;
                }

                break;
            case "App\Group":
                if (in_array($subscription->subscribable_id, auth()->user()->groups()->pluck('groups.id')->toArray())
                    and ($subscription->visibility == 1)) {
                    return true;
                }
                break;
            case "App\User":
                if ($subscription->subscribable_id == auth()->user()->id
                     and ($subscription->visibility == 1)) {
                    return true;
                }
                break;
            case "App\KanbanItem":
                if ($subscription->subscribable->kanban->isAccessible()) {
                    return true;
                }
                break;

            default: return false;
                break;
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'path' => 'sometimes',
            'subscribable_type' => 'sometimes',
            'subscribable_id' => 'sometimes',
            'repository' => 'sometimes',
            'artefact' => 'sometimes',
            'file.*' => 'sometimes|mimes:jpg,jpeg,png,gif,bmp,tiff,tif,ico,svg,mov,mp4,m4v,mpeg,mpg,mp3,m4a,m4b,wav,mid,avi,ppt,pps,pptx,doc,docx,pdf,xls,xlsx,xps,odt,odp,ods,odg,odc,odb,odf,key,numbers,pages,csv,txt,rtx,rtf,zip,psd,xcf',

            'title' => 'sometimes',
            'description' => 'sometimes',
            'author' => 'sometimes',
            'publisher' => 'sometimes',
            'city' => 'sometimes',
            'license_id' => 'sometimes',
            'public' => 'sometimes',
        ]);
    }
}
