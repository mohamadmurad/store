<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


trait ApiResponser{


    private function successResponse($data, $code){
        return response()->json($data,$code);
    }



    protected function errorResponse($message,$code=422){
        return response()->json(['error'=>$message,'code'=>$code],$code);
    }
/*
    protected function showAll(Collection $collection,$code = 200){
        if($collection->isEmpty()){
            return $this->successResponse(['data'=>$collection],$code);
        }
        //$transformer = $collection->first()->transformer;

        //$collection = $this->filterData($collection,$transformer);
        //$collection = $this->sortData($collection,$transformer);
        $collection = $this->paginate($collection);

       // $collection = $this->transformData($collection,$transformer);

        //$collection = $this->cacheResponse($collection);

        return $this->successResponse($collection,$code);
    }
*/

    private function showModel(JsonResource $jsonResource){
        return response()->json([
            'data' => [$jsonResource]
        ]);
    }
    protected function showCollection(AnonymousResourceCollection $collection,$paginate = true){
        //$collection = $this->filterData($collection);

        //$collection = $this->sortData($collection,$transformer);

        if ($paginate){
            $collection = $this->paginate($collection);
        }


        //$collection = $this->cacheResponse($collection);

        return $collection;

    }
    protected function showOne(Model $model,$code = 200){
        return $this->successResponse($model,$code);
    }

    protected function showMessage($message,$code = 200){
        return $this->successResponse(['data'=>$message],$code);
    }



    protected function sortData(Collection $collection,$transformer){
        if(request()->has('sort_by')){
            $attribute = $transformer::originalAttribute(request()->sort_by);
            $collection = $collection->sortBy->{$attribute};
            $collection = $collection->sortBy($attribute);
        }

        return $collection;
    }

    protected function paginate(AnonymousResourceCollection $collection){
        $rules = [
            'per_page' => 'integer|min:2|max:50',

        ];

        Validator::validate(request()->all(),$rules);

        $page = lengthAwarePaginator::resolveCurrentPage();

        $perPage = 10;

        if(request()->has('per_page')){
            $perPage = (int)request()->per_page;
        }

        $results = $collection->slice(($page - 1)* $perPage,$perPage)->values();

        $paginated = new LengthAwarePaginator($results,$collection->count(),$perPage,$page,[
            'path' => LengthAwarePaginator::resolveCurrentPath(),

        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function cacheResponse($data){
        $url = request()->url();
        return Cache::remember($url,30/60,function () use ($data){
           return $data;
        });

    }
}
