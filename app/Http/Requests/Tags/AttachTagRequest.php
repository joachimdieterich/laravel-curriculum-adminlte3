<?php

namespace App\Http\Requests\Tags;

class AttachTagRequest extends StoreTagRequest
{
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'taggable_id' => ['required', 'int']
            ]
        );
    }
}
