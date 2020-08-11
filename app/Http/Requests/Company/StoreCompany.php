<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompany extends FormRequest
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
        $rules =  [
            'name'=>'required|min:2|max:255|unique:'. $this->table .',name',
            'phone'=>'required|unique:'. $this->table .',phone',
            'logo'=>'',
            'category_id'=>'required|exists:categories,id',
        ];
        if ($this->has('logo')) {
            $rules['logo'] .= 'mimeTypes:image/jpeg,image/png';

        }
        return $rules;
    }
}
