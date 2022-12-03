<?php

namespace App\Domains\Exams\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_unless(\Gate::allows('test_create'), 403);
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
            'group_id'  => 'required|exists:groups,id',
            'test_id'   => 'required',
            'test_name' => 'required|string',
        ];
    }
}
