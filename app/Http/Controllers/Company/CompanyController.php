<?php

namespace App\Http\Controllers\Company;

use App\Companies;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Companies::all();

        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showAll($companies);
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
            'name'=>'required|min:2|max:255|unique:companies,name',
            'phone'=>'required|unique:companies,phone',
        ];

        $this->validate($request,$rules);

        $newCompany = Companies::create($request->only(['name','phone']));
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($newCompany);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Companies $company
     * @return \Illuminate\Http\Response
     */
    public function show(Companies $company)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->showOne($company);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Companies $company
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Companies $company)
    {
        $rules = [];
        if($request->has(['name'])){
            $rules += [
                'name'=>['min:2|max:100',
                    Rule::unique($company->getTable())->ignore(request()->segment(3))
                ],

            ];

            $company->fill($request->only([
                'name',
            ]));

        }
        if($request->has(['phone'])){
                $rules += [
                    'phone'=>['required',
                        Rule::unique($company->getTable())->ignore(request()->segment(3))
                        ],
                ];

            $company->fill($request->only([
                'phone',
            ]));
        }
        $this->validate($request,$rules);



        if($company->isClean()){
            return $this->errorResponse([
                'error'=> 'you need to specify a different value to update',
                'code'=> 422],
                422);
        }

        $company->save();
        return $this->showOne($company);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Companies $company
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Companies $company)
    {
        $company->delete();
        return $this->showOne($company);
    }
}
