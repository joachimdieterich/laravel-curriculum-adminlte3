<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('group_edit');
    }

    public function rules()
    {
        return [
            'title'    => [
                'sometimes',
            ]
        ];
    }
}
