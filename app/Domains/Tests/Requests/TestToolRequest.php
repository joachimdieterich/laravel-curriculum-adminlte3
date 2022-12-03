<?php

namespace App\Domains\Tests\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestToolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_unless(\Gate::allows('test_access'), 403);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tool' => 'required|in:' . implode(",", array_keys(config('test_tools.tools'))) . '',
        ];
    }
}
