<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTerminalObjectiveRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('objective_create');
    }

    public function rules()
    {
        
        return [
            'title' => [
                'required',
            ],
            'description' => [
                'sometimes',
            ],
            'color' => [ 
                'sometimes',
            ],
            'time_approach' => [
                'sometimes',
            ],
            'curriculum_id' => [ 
                'required',
            ],
            'objective_type_id'  => [
                'required',
            ],
             'visibility'  => [
                'sometimes',
            ],
        ];
    }
}
