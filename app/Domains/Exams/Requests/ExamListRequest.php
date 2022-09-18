<?php

namespace App\Domains\Exams\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamListRequest extends FormRequest
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
            'length'   => 'required|integer',
            'column'   => 'required|string',
            'dir'      => 'required|string',
            'search'   => 'nullable|string',
            'group_id' => 'required|exists:groups,id',
        ];
    }
}
