<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    private $table = 'users';
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
            'name'=>'required|string|min:5|max:100',
            'email'=>'required|email|max:255|unique:'. $this->table .',email',
            'phone'=>'required|unique:'. $this->table .',phone',
            'username'=>'required|unique:'. $this->table .',username',
            'location'=>'required|string',
            'password'=>'required|min:8|confirmed',
        ];
    }


}
