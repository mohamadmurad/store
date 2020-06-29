<?php

namespace App\Http\Requests\Attachment;

use App\AttachmentType;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttachment extends FormRequest
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
        $types = AttachmentType::all()->pluck('type')->join(',');

        return [
            'files'=>'required',
            'files.*'=>'required|mimeTypes:' . $types,
        ];
    }
}
