<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Companies;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompany;
use App\Http\Requests\Company\UpdateCompany;
use App\Http\Resources\Company\CompanyResource;
use App\Traits\ApiResponser;
use App\Traits\UploadAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    use ApiResponser,UploadAble;

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
            $saved_files_for_roleBack = [];
            DB::beginTransaction();
            try {
                $fileName = 'DefaultLogo.png';
                if ($request->has('logo')){
                    $logo = $request->file('logo');

                    $logo_file_saved = $this->upload($logo,public_path(config('app.COMPANY_LOGO_PATH','files/companyLogos/')));
                    $saved_files_for_roleBack += [$logo_file_saved->getFilename()];
                    $fileName = $logo_file_saved->getFilename();

                }

                $newCompany = Companies::create([
                    'name' => $request->get('name'),
                    'phone' => $request->get('phone'),
                    'category_id' => $request->get('category_id'),
                    'logo' => $fileName,

                ]);

                DB::commit();
            }catch (Exception $e){
                foreach ($saved_files_for_roleBack as $file){
                    File::delete(public_path(config('app.COMPANY_LOGO_PATH','files/companyLogos/')) . '/' . $file);
                }
                DB::rollBack();

                return $this->errorResponse('company doesnt added please try again' ,422);


            }

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
            return $this->showModel(new CompanyResource($company));
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
                'category_id',
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
