<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class DepositCardRequest extends FormRequest
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
            'balance'=>'required|integer|min:1',
            'cost'=>'required|integer|min:2',
        ];
    }

    protected function prepareForValidation()
    {
        return [
            'balance'=> $this->balance,
            'cost' => $this->cost,

        ];
    }
}
