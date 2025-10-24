<?php

namespace App\Http\Requests\Tags;

use App\Rules\ModelExits;
use Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('tag_create');
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'global' => $this->global ?? false
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => ['string', new ModelExits()],
            'global' => ['boolean'],
        ];
    }
}
