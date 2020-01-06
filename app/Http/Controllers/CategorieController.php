<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categorie;

class CategorieController extends Controller
{
    public function index()
    {
        $categories = Categorie::all();
        // axios call?
        if (request()->wantsJson()){
            return [
                'message' => $categories
            ];
        }
        return $categories;
    }
    
    public function list()
    {
        
    }
    
    public function create()
    {
        
    }
    
    public function store()
    {
        abort_unless(\Gate::allows('categorie_create'), 403);
        
        $categorie = Categorie::firstOrCreate($this->validateRequest());
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => Categorie::all()];
        }
        
        return redirect($categorie->path());
    }
    
    public function show(Group $categorie)
    { 
        
    }
    
    public function update(UpdateGroupRequest $request, Categorie $categorie)
    {
        
    }
    
    public function destroy(Categorie $categorie)
    {
        
    }
    
    protected function validateRequest()
    {    
        return request()->validate([
            'title' => 'required',
        ]);
    }
    
}
