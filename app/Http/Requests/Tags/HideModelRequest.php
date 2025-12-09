<?php

namespace App\Http\Requests\Tags;

use Illuminate\Foundation\Http\FormRequest;

class HideModelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mark' => 'required|boolean',
        ];
    }
}
