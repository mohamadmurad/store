<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Branch;
use App\Companies;
use App\Http\Controllers\Controller;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Company\CompanyBranchResource;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyBranchController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @param Companies $company
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Pagination\LengthAwarePaginator|void
     */
    public function index(Companies $company)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $branches = $company->branches()->get();

            return $this->showCollection(CompanyBranchResource::collection($branches));
        }

        return null;


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branches $branch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branches $branch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branches $branch)
    {
        //
    }
}
