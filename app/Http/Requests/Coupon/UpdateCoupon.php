<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoupon extends FormRequest
{
    private $table = 'coupons';
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
            'discountRate'=>'required|integer|min:1|max:100',
        ];
    }

    protected function prepareForValidation()
    {
        return [
            'discountRate'=> (int) $this->discountRate,
        ];
    }
}
