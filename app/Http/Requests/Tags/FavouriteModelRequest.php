<?php

namespace App\Http\Requests\Tags;

use Illuminate\Foundation\Http\FormRequest;

class FavouriteModelRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'favourite' => 'required|boolean',
        ];
    }
}
