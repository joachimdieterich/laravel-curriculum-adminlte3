<?php

namespace App\Http\Requests;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return \Gate::allows('user_create');
    }

    public function rules()
    {
        return [
            'username'     => [
                'required',
            ],
            'email'    => [
                'required',
            ],
            'password' => [
                'required',
            ]
        ];
    }
}
