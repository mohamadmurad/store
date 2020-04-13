<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCard extends FormRequest
{

    private $table = 'cards';
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
            'balance'=>'required|integer|min:0',
        ];
    }

    protected function prepareForValidation()
    {
        return [
            'balance'=> (float) $this->balance,
        ];
    }
}
