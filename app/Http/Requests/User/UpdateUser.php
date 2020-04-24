<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUser extends FormRequest
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
        $rules = [
            'password'=>'required|min:8',
        ];
        if($this->has(['name'])){
            $rules += [
                'name'=>'required|string|min:5|max:100',
            ];
        }
        if($this->has(['email'])){
            $rules += [
                'email' =>[
                    'required',
                    'email',
                    'max:255',
                    Rule::unique($this->table)->ignore(request()->segment(3))
                ],
            ];

        }
        if($this->has(['phone'])){
            $rules += [
                'phone'=>[
                    'required',
                    Rule::unique($this->table)->ignore(request()->segment(3))
                ],

            ];
        }

        if($this->has(['username'])){
            $rules += [
                'username'=>[
                    'required',
                    Rule::unique($this->table)->ignore(request()->segment(3))
                ],
            ];

        }

        if($this->has(['location'])){
            $rules += [
                'location'=>'required|string',
            ];
        }


        if($this->has(['newPassword'])){
            $rules += [
                'newPassword'=>'required|min:8|confirmed|different:password',
            ];
        }
        return $rules;
    }
}
