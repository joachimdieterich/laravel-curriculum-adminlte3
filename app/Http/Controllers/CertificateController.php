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

        $certificates = Certificate::all();
       
        return view('certificates.index')
          ->with(compact('certificates'));
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
            ]);
        
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
    public function create()
    {
        abort_unless(\Gate::allows('certificate_create'), 403);
        
        $curricula = Curriculum::all();
        $organisations = Organization::all();
        
        return view('certificates.create')
                ->with(compact('curricula'))
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
        $curricula = Curriculum::all();
        $organisations = Organization::all();
        
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
        $certificate = Certificate::find(request()->certificate_id)->get(); 
        
        foreach ((array) request()->user_ids as $id) 
        {
            
            $user = User::where('id', $id)->get()->first();
            $date = request()->date;
            $timestamp = date("Y-m-d_H-i-s");
            $html_to_print = $certificate->first()->body;
            //replace placeholder
            $html_to_print = str_replace('{{$firstname}}', $user->firstname, $html_to_print);
            $html_to_print = str_replace('{{$lastname}}', $user->lastname, $html_to_print);
            $html_to_print = str_replace('{{$date}}', $date, $html_to_print);
            $html_to_print = preg_replace_callback( 
                '/<progress\s+[^>]*reference_type="(.*?)"\s+[^>]*reference_id="(.*?)"\s+[^>]*min_value="(.*?)"[^>]*>(.*?)<\/progress>/mis', 
                function($match) use($user)
                { 
                    // evaluate progress
                    // Example
                    // Full match	<progress reference_type="App\TerminalObjective" reference_id="1" min_value="60"/><img src="/media/2"/></progress>
                    // Group 1.	App\TerminalObjective
                    // Group 2.	1
                    // Group 3.	60
                    // Group 4.	<img src="/media/2"/>

                    $associable_type = 'App\User';
                    $associable_id = $user->id;

    //             foreach(explode(",", $match[2]) as $terid) // recalc progress -> only for dev 
    //             {
    //                (new ProgressController)->calculateProgress('App\TerminalObjective', $terid, $associable_id);
    //             }

                    $progress = \App\Progress::where('referenceable_type', $match[1])
                                ->whereIn('referenceable_id', explode(",", $match[2]))
                                ->where('associable_type', $associable_type)
                                ->where('associable_id', $associable_id)
                                ->get();
                    return ($progress->avg('value') != null AND $progress->avg('value') >= (integer) $match[3]) ? $match[4] : '';   
                }, 

                $html_to_print 
            );     
            //end progress

            /* replace relative media links with absolute paths to get snappy working */ 
            $html_to_print = preg_replace_callback( 
                '/<img\s+[^>]*src="\/media\/(.*?)"(\s+[^>]*)[^>]*>/mi', 
                function($match) 
                { 
                    $media = Medium::find($match[1]);
                    return (( "<img src=\"{$media->absolutePath()}\"{$match[2]}>"));      
                }, 
                $html_to_print 
            ); 
            
            //return SnappyPdf::loadHTML($html_to_print)
            SnappyPdf::loadHTML($html_to_print)
                    ->setPaper('a4')
                   // ->setOrientation('landscape')
                    ->setOption('margin-bottom', 0)
                    ->save(storage_path("app/users/".auth()->user()->id."/".$timestamp.$user->lastname."_".$user->firstname.".pdf"));
            
            
            $media = new Medium([
                'path'          => "/users/".auth()->user()->id."/".date("Y-m-d_H-i-s").$user->lastname."_".$user->firstname.".pdf",
                'title'         => $user->lastname."_".$user->firstname.".pdf",
                'medium_name'   => $user->lastname."_".$user->firstname.".pdf",
                'description'   => $user->lastname."_".$user->firstname.".pdf",
                'author'        => auth()->user()->fullName(),
                'publisher'     => '',
                'city'          => '',
                'date'          => date("Y-m-d_H-i-s"),
                'size'          => File::size(Storage::disk('local')->path("users/".auth()->user()->id."/".$timestamp.$user->lastname."_".$user->firstname.".pdf")),
                'mime_type'     => File::mimeType(Storage::disk('local')->path("users/".auth()->user()->id."/".$timestamp.$user->lastname."_".$user->firstname.".pdf")),
                'license_id'    => 2,//$media_node->getAttribute('license'), //hack fix false entries in import files

                'owner_id'      => auth()->user()->id,

            ]); 
            $media->save();
            
        }
                //->inline('cur.pdf');
        //return back();
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
