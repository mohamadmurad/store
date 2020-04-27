<?php

namespace App\Http\Controllers\Api\V1\Attachment;

use App\AttachmentType;
use App\Attributes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\StoreAttachmentType;
use App\Http\Requests\Attachment\UpdateAttachmentType;
use App\Http\Resources\Attachment\AttachmentTypeResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttachmentTypeController extends Controller
{

    use  ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|Response|LengthAwarePaginator
     */
    public function index()
    {

        if (request()->expectsJson() && request()->acceptsJson()){
            $attachmentType = AttachmentType::all();
            return $this->showCollection(AttachmentTypeResource::collection($attachmentType));
        }

        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAttachmentType $request
     * @return AttachmentTypeResource|Response
     */
    public function store(StoreAttachmentType $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $newAttachmentType = AttachmentType::create($request->only(['type']));
            return new AttachmentTypeResource($newAttachmentType);
        }
        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param AttachmentType $attachmentType
     * @return AttachmentTypeResource|Response
     */
    public function show(AttachmentType $attachmentType)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new AttachmentTypeResource($attachmentType);
        }
        return null;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAttachmentType $request
     * @param AttachmentType $attachmentType
     * @return AttachmentTypeResource|JsonResponse|Response
     */
    public function update(UpdateAttachmentType $request, AttachmentType $attachmentType)
    {

        if (request()->expectsJson() && request()->acceptsJson()){
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
            return new AttachmentTypeResource($attachmentType);
        }
        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AttachmentType $attachmentType
     * @return AttachmentTypeResource|Response
     * @throws Exception
     */
    public function destroy(AttachmentType $attachmentType)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $attachmentType->delete();
            return new AttachmentTypeResource($attachmentType);
        }

        return null;

    }
}
