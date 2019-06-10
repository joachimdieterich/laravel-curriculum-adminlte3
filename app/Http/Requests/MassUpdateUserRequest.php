<?php

namespace App\Http\Requests;

use App\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassUpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return abort_if(Gate::denies('user_reset_password'), 403, '403 Forbidden') ?? true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:users,id',
            'password' => 'sometimes|string|min:8',
            'status_id' => 'sometimes',
        ];
    }
}
