<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranch extends FormRequest
{

    private $table = 'branches';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'name'=>'required|min:2|max:255',
            'location'=>'required|min:2|max:100',
            'phone' => 'required',
            'company_id'=>'required|integer|exists:companies,id',
            'user_id'=>'required|integer|exists:users,id',
        ];
    }
}
