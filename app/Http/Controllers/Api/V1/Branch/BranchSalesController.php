<?php

namespace App\Http\Controllers\Api\V1\Branch;

use App\Http\Controllers\Controller;
use App\branches;
use App\Http\Requests\Branch\StoreBranch;
use App\Http\Requests\Branch\UpdateBranch;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\product\ProductResource;
use App\Http\Resources\product\WebProductResource;
use App\Traits\ApiResponser;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;

class BranchSalesController extends Controller
{

    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection|LengthAwarePaginator
     */
    public function index(Branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()){



            $product = $branch->products()->has('sales')->with(['branch','firstAttachments','sales'])->get();
            return $this->showCollection(WebProductResource::collection($product));
        }

        return null;
    }

}
