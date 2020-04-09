<?php

namespace App\Http\Controllers\Attachment;

use App\AttachmentType;
use App\Attributes;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttachmentController extends Controller
{

    use  ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attachmentType = AttachmentType::all();
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($attachmentType);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $rules = [
            'type'=>'required|min:2|max:100|unique:attachment_types,type',
        ];

        $this->validate($request,$rules);

        $newAttachmentType = AttachmentType::create($request->only(['type']));
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newAttachmentType);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AttachmentType  $attachmentType
     * @return \Illuminate\Http\Response
     */
    public function show(AttachmentType $attachmentType)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($attachmentType);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\AttachmentType $attachmentType
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, AttachmentType $attachmentType)
    {
        $rules = [
            'type'=>['min:2|max:100'
                , Rule::unique($attachmentType->getTable())->ignore(request()->segment(3))
            ],

        ];

        $this->validate($request,$rules);

        $attachmentType->fill($request->only([
            'type',
        ]));

        if($attachmentType->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }

        $attachmentType->save();
        return $this->showOne($attachmentType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\AttachmentType $attachmentType
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(AttachmentType $attachmentType)
    {
        $attachmentType->delete();
        return $this->showOne($attachmentType);
    }
}
