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
            'username'    => [
                'required',
            ],
            'email'   => [
                'required',
            ],
            'firstname'   => [
                'required',
            ],
            'lastname'   => [
                'required',
            ],
            'password'   => [
                'sometimes',
            ],
             
        ];
    }
}
