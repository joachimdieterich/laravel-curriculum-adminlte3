<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class MassDestroyOrganizationRequest extends FormRequest
{
    public function authorize()
    {
        return abort_if(Gate::denies('organization_delete'), 403, '403 Forbidden') ?? true;
    }

    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'exists:organizations,id',
        ];
    }
}
