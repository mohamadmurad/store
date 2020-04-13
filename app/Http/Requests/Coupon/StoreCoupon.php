<?php

namespace App\Http\Requests\Coupon;

use App\Coupons;
use Illuminate\Foundation\Http\FormRequest;

class StoreCoupon extends FormRequest
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
            'discountRate'=>'required|integer|min:1',
            'code' => 'min:6|max:6',
        ];
    }

    protected function prepareForValidation()
    {
        if($this->has('code')){
            $this->code = $this->get('code');
        }else{
            $this->code = Coupons::randomCouponCode();

        }

        return [
            'discountRate'=> (int) $this->discountRate,
            'code' => $this->code,
        ];
    }
}
