<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\Curriculum;
use App\Organization;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
        
        
        return back();
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
