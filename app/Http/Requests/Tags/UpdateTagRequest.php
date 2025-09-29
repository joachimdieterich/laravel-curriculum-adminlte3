<?php

namespace App\Http\Requests\Tags;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('tag_edit');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => 'nullable|string',
        ];
    }
}
