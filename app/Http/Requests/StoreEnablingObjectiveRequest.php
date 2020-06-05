<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnablingObjectiveRequest extends FormRequest
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
            'time_approach' => [
                'sometimes',
            ],
            'curriculum_id' => [ 
                'required',
            ],
            'terminal_objective_id'  => [
                'required',
            ],
            'visibility'  => [
                'sometimes',
            ],
        ];
    }
}
