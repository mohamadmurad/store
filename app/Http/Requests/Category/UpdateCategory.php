<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategory extends FormRequest
{

    private $table = 'categories';
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
            'name'=>[
                'required',
                'min:2',
                'max:100',
                Rule::unique($this->table)->ignore(request()->segment(4))
            ],
            'parent_id'=>'required',
        ];

        if ($this->parent_id !== null){
            $rules['parent_id'] .= '|exists:'. $this->table .',id';
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->parent_id === 'null'){
            $this->parent_id = null;
        }
        return [
            'name' => $this->name,
            'parent_id' => $this->parent_id,
        ];
    }
}
