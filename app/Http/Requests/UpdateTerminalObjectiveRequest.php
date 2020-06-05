<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTerminalObjectiveRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('objective_edit');
    }

    public function rules()
    {
        return [
            'title' => [
                'sometimes',
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
                'sometimes',
            ],
            'objective_type_id'  => [
                'sometimes',
            ],
            'order_id'  => [
                'sometimes',
            ],
             'visibility'  => [
                'sometimes',
            ],
          
        ];
    } 
      
}
