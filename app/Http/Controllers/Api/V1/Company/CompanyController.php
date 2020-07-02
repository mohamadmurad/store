<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Companies;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompany;
use App\Http\Requests\Company\UpdateCompany;
use App\Http\Resources\Company\CompanyResource;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        $this->middleware(['permission:add_company'])->only('store');
        $this->middleware(['permission:edit_company'])->only('update');
        $this->middleware(['permission:delete_company'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|LengthAwarePaginator
     */
    public function index()
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $companies = Companies::all();
            return $this->showCollection(CompanyResource::collection($companies));
        }

        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompany $request
     * @return CompanyResource|JsonResponse
     */
    public function store(StoreCompany $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $newCompany = Companies::create($request->only(['name','phone']));
            return $this->successResponse([
                'message' => 'Company added Successful',
                'code' => 201,
            ],201);
        }

        return null;

    }

    /**
     * Display the specified resource.
     *
     * @param Companies $company
     * @return CompanyResource
     */
    public function show(Companies $company)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return new CompanyResource($company);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCompany $request
     * @param Companies $company
     * @return CompanyResource|JsonResponse|Response
     */
    public function update(UpdateCompany $request, Companies $company)
    {


        if (request()->expectsJson() && request()->acceptsJson()){
            $company->fill($request->only([
                'name',
                'phone',
            ]));

            if($company->isClean()){
                return $this->errorResponse([
                    'error'=> 'you need to specify a different value to update',
                    'code'=> 422],
                    422);
            }

            $company->save();
            return $this->successResponse([
                'message' => 'Company Updated Successful',
                'code' => 200,
            ],200);

        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Companies $company
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Companies $company)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            $company->delete();
            return $this->successResponse([
                'message' => 'Company Deleted Successful',
                'code' => 200,
            ],200);
        }
        return null;
    }
}
