<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('user_edit');
    }

    public function rules()
    {
        return [
            'name'    => [
                'sometimes|required',
            ],
            'email'   => [
                'sometimes|required',
            ],
            'roles.*' => [
                'sometimes|integer',
            ],
            'roles'   => [
                'sometimes|required',
                'array',
            ],
        ];
    }
}
