<?php

namespace App\Http\Requests\Sale;

use Illuminate\Foundation\Http\FormRequest;

class StoreSale extends FormRequest
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

        $fieldName = $this->attributes();

        return [
            $fieldName['saleRate'] => 'required|max:100|min:1',
            $fieldName['newPrice'] => '',
            $fieldName['start'] => 'required|date',
            $fieldName['end'] => 'required|date',
            $fieldName['product_id'] => [
                'required',
                'exists:products,id',
                'unique:sales,product_id,null,id,deleted_at,NULL',
            ],
         ];
    }

    public function attributes(){
        return [
            'saleRate' => 'sale',
            'newPrice' => 'price',
            'start' => 'start',
            'end' => 'end',
            'product_id' => 'product',
        ];
    }
}
