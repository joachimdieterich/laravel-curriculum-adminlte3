<?php

namespace App\Domains\Exams\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExamRemoveUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        abort_unless(\Gate::allows('test_edit'), 403);
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
            'expel_list'   => 'required|exists:users,id',
        ];
    }
}
