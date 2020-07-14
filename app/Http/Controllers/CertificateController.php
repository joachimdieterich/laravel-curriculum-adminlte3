<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\Curriculum;
use App\Medium;
use App\Organization;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use \Barryvdh\Snappy\Facades\SnappyPdf;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('certificate_access'), 403);

        //$certificates = Certificate::where('owner_id', auth()->user()->id)->get();
       
        return view('certificates.index');
          //->with(compact('certificates'));
    }
    
    public function list()
    {
        abort_unless(\Gate::allows('certificate_access'), 403);
        $certificates = Certificate::select([
            'id', 
            'title', 
            'description',
            'body',
            'curriculum_id',
            'organization_id',
            'owner_id',
            ])->where('owner_id', auth()->user()->id)->get();
        
        return DataTables::of($certificates)
            ->addColumn('organization', function ($certificates) {
                return $certificates->organization()->first()->title;                
            })
            ->addColumn('curriculum', function ($certificates) {
                return $certificates->curriculum()->first()->title;                
            })
            ->addColumn('owner', function ($certificates) {
                return $certificates->owner()->first()->firstname.' '.$certificates->owner()->first()->lastname;                
            })
            ->addColumn('action', function ($certificates) {
                 $actions  = '';
                    if (\Gate::allows('certificate_show')){
                        $actions .= '<a href="'.route('certificates.show', $certificates->id).'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i> Show'
                                    . '</a>';
                    }
                    if (\Gate::allows('certificate_edit')){
                        $actions .= '<a href="'.route('certificates.edit', $certificates->id).'" '
                                    . 'class="btn btn-xs btn-primary mr-1">'
                                    . '<i class="fa fa-edit"></i> Edit'
                                    . '</a>';
                    }
                    if (\Gate::allows('certificate_delete')){
                        $actions .= '<button type="button" class="btn btn-xs btn-danger" onclick="destroyCertificate('.$certificates->id.')"><i class="fa fa-trash"></i> Delete</button>';
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
    public function create(Request $request)
    {
        abort_unless(\Gate::allows('certificate_create'), 403);
        
        $curricula = Curriculum::where('id', $request->query('curriculum_id'))->get();
        $organisations = auth()->user()->organizations()->get();
        
        $certificate = new Certificate();
        $certificate->curriculum_id = $request->query('curriculum_id');
        $certificate->organization_id = auth()->user()->current_organization_id;
        
        return view('certificates.create')
                ->with(compact('curricula'))
                ->with(compact('certificate'))
                ->with(compact('organisations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('certificate_create'), 403);
        $input = $this->validateRequest();
        //dd($input);
        $certificate = Certificate::firstOrCreate([
            'title'           => $input['title'],
            'description'     => $input['description'],
            'body'            => $input['body'],
            'curriculum_id'   => format_select_input($input['curriculum_id']),
            'organization_id' => format_select_input($input['organization_id']),
            'owner_id'        => auth()->user()->id,
            
        ]);
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $certificate->path()];
        }
        
        return redirect($certificate->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function show(Certificate $certificate)
    {
        return view('certificates.show')
         ->with(compact('certificate'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function edit(Certificate $certificate)
    {
        abort_unless(\Gate::allows('certificate_edit'), 403);
        $curricula = Curriculum::where('owner_id', auth()->user()->id)->get();
        $organisations = auth()->user()->organizations()->get();
        
        return view('certificates.edit')
            ->with(compact('certificate'))
            ->with(compact('curricula'))
            ->with(compact('organisations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Certificate $certificate)
    {
        abort_unless(\Gate::allows('certificate_edit'), 403);
        
        $certificate->update([
            'title' => $request['title'],
            'description' => $request['description'],
            'body' => $request['body'],
            'curriculum_id' => format_select_input($request['curriculum_id']),
            'organization_id' => format_select_input($request['organization_id']),
        ]);

        return redirect()->route('certificates.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Certificate  $certificate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Certificate $certificate)
    {
        abort_unless(\Gate::allows('certificate_delete'), 403);

        $certificate->delete();

        return back();
    }
    
    /**
     * Generate a certificate based on existing template
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        abort_unless(\Gate::allows('certificate_create'), 403);
        $certificate = Certificate::find(request()->certificate_id); 
        
        switch ($certificate->type) 
        {
            case 'user':            return $this->generateForUsers($certificate);
                break;
            case 'group':           return $this->generateForGroup($certificate);
                break;
            case 'organization':    return $this->generateForOrganization($certificate);
                break;

            default:
                break;
        }
    }
    
    /**
     * Generate certificate(s) for user(s)
     * @param object $certificate
     * @return string medium->path()
     */
    protected function generateForUsers($certificate)
    {
        $generated_files = [];
        foreach ((array) request()->user_ids as $id) 
        {   
            $user = User::where('id', $id)->get()->first();
            
            //replace placeholder
            $html = $this->replaceFields(
                    $certificate->body, 
                    $user, 
                    Organization::where('id', auth()->user()->current_organization_id)->get()->first(), 
                    request()->date);
           
            $html = preg_replace_callback( 
                '/<progress\s+[^>]*reference_type="(.*?)"\s+[^>]*reference_id="(.*?)"\s+[^>]*min_value="(.*?)"[^>]*>(.*?)<\/progress>/mis', 
                function($match) use($user)
                { 
                    // evaluate progress
                    // Example
                    // Full match <progress reference_type="App\TerminalObjective" reference_id="1" min_value="60"/><img src="/media/2"/></progress>
                    // Group 1 | $match[1] App\TerminalObjective
                    // Group 2 | $match[2] 1
                    // Group 3 | $match[3] 60
                    // Group 4 | $match[4] <img src="/media/2"/>

                    $associable_type = 'App\User';
                    $associable_id = $user->id;

                    // foreach(explode(",", $match[2]) as $terid) // recalc progress -> only for dev 
                    // {
                    //      (new ProgressController)->calculateProgress('App\TerminalObjective', $terid, $associable_id);
                    // }

                    $progress = \App\Progress::where('referenceable_type', $match[1])
                                ->whereIn('referenceable_id', explode(",", $match[2]))
                                ->where('associable_type', $associable_type)
                                ->where('associable_id', $associable_id)
                                ->get();
                    
                    return ($progress->avg('value') != null AND $progress->avg('value') >= (integer) $match[3]) 
                            ? $match[4] 
                            : $match[4].'<div style="display: block; position: absolute; top:0; height:100%; width: 140px; background: white; opacity: 0.8;"></div>';
                }, 

                $html 
            );     
            //end progress

            $filename = date("Y-m-d_H-i-s").$user->lastname."_".$user->firstname.".pdf";
            $path     = config('lfm.files_folder_name')."/".auth()->user()->id."/";
            $this->buildPdf($html, $path, $filename);
            
            array_push($generated_files, ['filename' => $filename, 'path' => Storage::disk('local')->path($path.$filename)]);
        }//end foreach
        
        if (request()->wantsJson()){    
            return ['message' => $this->zipper($path, $generated_files)];
        }
    }
    
    /**
     * Generate certificate for group
     * @param object $certificate
     */
    protected function generateForGroup($certificate)
    {
        $td_style   = 'style="border-bottom: 1px solid silver;border-right: 1px solid silver;"';
        
        $html  = '<table repeat_header="1" style="width: 100%;padding-bottom: 10px;" border="0"><tbody>'
                .'<thead><tr><td style="border-bottom: 1px solid silver;"><strong>Ziele / Namen</strong></td>';
        foreach ((array) request()->user_ids as $id) 
        {   
            $user = User::where('id', $id)->get()->first();
            $html .= '<td '.$td_style.'><strong>'.$user->firstname.' '.$user->lastname.'</strong></td>';
        }
        $html .= '</tr></thead>';
        
        $curriculum = Curriculum::with([
                'terminalObjectives', 
                'terminalObjectives.enablingObjectives'])
            ->find($certificate->curriculum_id);
                                
        foreach ($curriculum->terminalObjectives as $ter_value) 
        {
            $html .= '<tr><td '.$td_style.'><strong>'.strip_tags($ter_value->title).'</strong></td>';
            foreach((array) request()->user_ids as $id)
            {
                $html .= '<td '.$td_style.'></td>';
            }
            $html .= '</tr>';
            foreach ($ter_value->enablingObjectives as $ena) 
            {
                
                $html .= '<tr><td style="width: 25%;border-bottom: 1px solid silver;border-right: 1px solid silver;">'.$ena->id.strip_tags($ena->title).'</td>';
                    foreach((array) request()->user_ids as $user_id)
                    {
                        $html .='<td style="text-align: center; border-bottom: 1px solid silver;border-right: 1px solid silver;">';
                        $html .= $this->achievementIndicator(optional(\App\Achievement::where(
                            [
                                "referenceable_type" => 'App\EnablingObjective',
                                "referenceable_id"   => $ena->id,
                                "user_id"            => $user_id,                        
                            ])->get()->first())->status);
                        
                        $html .= '</td>';
                    }
                $html .= '</tr>';
            }
        }
        
        $timestamp = date("Y-m-d_H-i-s");
        $html .='</tbody></table>';
        $filename = $timestamp.$user->lastname."_".$user->firstname.".pdf";
        $path = config('lfm.files_folder_name')."/".auth()->user()->id."/";
        
        if (request()->wantsJson()){    
            return ['message' => $this->buildPdf($html, $path, $filename, 'landscape')];
        }
    }
    
    
    protected function replaceFields($string, $user, $organization, $date)
    {
        $search = array(
            '{{$firstname}}', 
            '{{$lastname}}', 
            '{{$organization_title}}',
            '{{$organization_street}}',
            '{{$organization_postcode}}', 
            '{{$organization_city}}', 
            '{{$date}}');
        
        $replace = array(
            $user->firstname, 
            $user->lastname, 
            $organization->title, 
            $organization->street, 
            $organization->postcode, 
            $organization->city, 
            $date);
        
        return str_replace($search, $replace, $string);
    }
    
    protected function achievementIndicator($status)
    {
        $span_style = 'style="text-align: center; font-family: Arial Unicode MS, Lucida Grande"';

        switch (true) {
            case in_array($status, array("01","11","21","31")): $html ='<span '.$span_style.'>&#10004;</span>';
                break;
            case in_array($status, array("02","12","22","32")): $html ='<span '.$span_style.'>(&#10004;)</span>';
                break;
            case in_array($status, array("03","13","23","33")): $html ='<span '.$span_style.'>&#10007;</span>';
                break;

            default:  $html ='<span '.$span_style.'></span>';
                break;
        }
        return $html;
    }
    
    /**
     * Generate certificate for organization
     * @param object $certificate
     */
    protected function generateForOrganization($certificate)
    {
        //todo
    }
    
    protected function buildPdf($html, $path, $filename, $orientation = 'portrait')
    {
        /* replace relative media links with absolute paths to get snappy working */ 
        $html = relativeToAbsoutePaths($html);

        SnappyPdf::loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'.$html)
                ->setPaper('a4')
                ->setOrientation($orientation)
                ->setOption('margin-bottom', 0)
                ->save(storage_path("app/".$path.$filename));

        return $this->addFileToDb($filename);
    }
    
    protected function zipper($path, $files)
    {
        $filename = date("Y-m-d_H-i-s").".zip";
        $zip_file = storage_path("app/".$path.$filename);
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        foreach ($files as $file) {
            $zip->addFile($file['path'], $file['filename']);
        }
        
        $zip->close();
        
        return $this->addFileToDb($filename); //returns $media->path()
    }

    protected function addFileToDb($filename)
    {
        $media = new Medium([
                'path'          => "/".config('lfm.files_folder_name')."/".auth()->user()->id."/",
                'title'         => $filename,
                'medium_name'   => $filename,
                'description'   => '',
                'author'        => auth()->user()->fullName(),
                'publisher'     => '',
                'city'          => '',
                'date'          => date("Y-m-d_H-i-s"),
                'size'          => File::size(Storage::disk('local')->path(config('lfm.files_folder_name')."/".auth()->user()->id."/".$filename)),
                'mime_type'     => File::mimeType(Storage::disk('local')->path(config('lfm.files_folder_name')."/".auth()->user()->id."/".$filename)),
                'license_id'    => 2,

                'owner_id'      => auth()->user()->id,
            ]); 
        $media->save();
        return  $media->path();
    }

    protected function validateRequest()
    {               
        return request()->validate([
            'title'                 => 'sometimes|required',
            'description'           => 'sometimes',
            'body'                  => 'sometimes',
            'curriculum_id'         => 'sometimes',
            'organization_id'       => 'sometimes',
        ]);
    }
}
