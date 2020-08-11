<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompany extends FormRequest
{

    private $table = 'companies';
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

        $rules = [



        ];

        if ($this->has('name')){

            $rules['name'] = [
                'required',
                'min:2',
                'max:100',
                Rule::unique($this->table)->ignore(request()->segment(4))
            ];
        }


        if ($this->has('phone')){

            $rules['category_id'] = [
                'required',
                Rule::unique($this->table)->ignore(request()->segment(4))
            ];
        }


        if ($this->has('category_id')){
            $rules['category_id'] = 'required|exists:categories,id';
        }
        return $rules;
    }
}
