<?php

namespace App\Http\Requests\Card;

use Illuminate\Foundation\Http\FormRequest;

class StoreCard extends FormRequest
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
            'user_id'=>'required|integer|exists:users,id',
        ];
    }

    protected function prepareForValidation()
    {
        return [
            'balance'=> (float) $this->balance,
            'user_id'=> (int) $this->user_id,
        ];
    }
}
