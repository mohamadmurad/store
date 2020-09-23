<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSale extends FormRequest
{
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
            'saleRate' => 'required_without:newPrice|max:99|min:1|numeric',
            'newPrice' => 'required_without:saleRate|numeric',
            'start' => 'required|date',
            'end' => 'required|date',
        ];
    }
}
