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
     * @return CompanyResource
     */
    public function store(StoreCompany $request)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $newCompany = Companies::create($request->only(['name','phone']));
            return new CompanyResource($newCompany);
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
            return new CompanyResource($company);

        }

        return null;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Companies $company
     * @return CompanyResource|null
     * @throws Exception
     */
    public function destroy(Companies $company)
    {
        if (request()->expectsJson() && request()->acceptsJson()) {
            $company->delete();
            return new CompanyResource($company);
        }
        return null;
    }
}
