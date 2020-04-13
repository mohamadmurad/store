<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranch extends FormRequest
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
            'name'=>[
                'required',
                'min:2',
                'max:100',
                Rule::unique($this->table)->ignore(request()->segment(3))
            ],
            'location'=>[
                'required',
                'min:2',
                'max:100',
                Rule::unique($this->table)->ignore(request()->segment(3))
            ],
            'balance'=>'required|integer|min:0',
            'user_id'=>'required|integer|exists:users,id',
            'company_id'=> 'required|integer|exists:companies,id',

        ];
    }
}
