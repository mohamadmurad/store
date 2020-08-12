<?php

namespace App\Http\Controllers\Api\V1\Branch;

use App\Http\Controllers\Controller;
use App\branches;
use App\Http\Requests\Branch\StoreBranch;
use App\Http\Requests\Branch\UpdateBranch;
use App\Http\Resources\Branch\BranchResource;
use App\Http\Resources\Offer\OfferResource;
use App\Http\Resources\product\ProductResource;
use App\Http\Resources\product\WebProductResource;
use App\Offers;
use App\Traits\ApiResponser;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use function foo\func;

class BranchOfferController extends Controller
{

    use ApiResponser;

    /**
     * Display a listing of the resource.
     *
     * @param branches $branch
     * @return AnonymousResourceCollection|LengthAwarePaginator
     */
    public function index(Branches $branch)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            $branch_id = $branch->id;
            $offers = Offers::whereHas('products',function($q) use ($branch_id){
                $q->where('branch_id','=',$branch_id);
            })->with('products')->get();

            return $this->showCollection(OfferResource::collection($offers));
        }

        return null;
    }

}
