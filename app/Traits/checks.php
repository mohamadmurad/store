<?php
namespace App\Traits;

use App\Products;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


trait checks{

    function checkIfUserHasProduct(User $user,Products $product){
        $branch_product  = $product->branch()->first();

        if ($branch_product->user_id !== $user->id){
            return false;
        }
        return true;
    }
}
