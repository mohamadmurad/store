<?php

namespace App\Http\Requests\Attachment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttachmentType extends FormRequest
{

    private $table = 'attachment_types';
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
            'type'=>[
                'required',
                'min:2',
                'max:100',
                Rule::unique($this->table)->ignore(request()->segment(3))
            ],
        ];
    }
}
