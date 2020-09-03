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

        $rules = [];

        if ($this->has('name')){
            $rules+= [
                'name'=> [
                    'required',
                    'min:2',
                    'max:100',
                    //Rule::unique($this->table)->ignore(request()->segment(4))
                    ]

            ];
        }

        if ($this->has('location')){
            $rules+= [
                'location'=>[
                    'required',
                    'min:2',
                    'max:100',
                   // Rule::unique($this->table)->ignore(request()->segment(4))
                ]

            ];
        }

        if ($this->has('balance')){
            $rules+= [
                'balance'=>'required|integer|min:0',
            ];
        }


        if ($this->has('user_id')){
            $rules+= [
                'user_id'=>'required|integer|exists:users,id',
            ];
        }


        if ($this->has('company_id')){
            $rules+= [
                'company_id'=> 'required|integer|exists:companies,id',
            ];
        }


        return $rules;
    }
}
