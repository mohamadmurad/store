<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (request()->expectsJson() && request()->acceptsJson()){
            return $this->apiHandleException($request,$exception);
        }
        return parent::render($request, $exception);
    }

    /**
     * Handle api exceptions
     * @param $request
     * @param Throwable $exception
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function apiHandleException($request,Throwable $exception){

        if ($exception instanceof ThrottleRequestsException){
            return $this->errorResponse('Too Many Attempts',429);
        }
        // validation ....
        if ($exception instanceof ValidationException){
            return $this->convertValidationExceptionToResponse($exception,$request);
        }
        // model not found ...
        if ($exception instanceof ModelNotFoundException){
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does not exist any ". $modelName ." with the specific id",404);
        }
        // login ....
        if ($exception instanceof AuthenticationException){
            return $this->unauthenticated($request, $exception);
        }
        // admin , user .....
        if ($exception instanceof AuthorizationException){
            return $this->errorResponse($exception->getMessage(),403);
        }
        // url ....
        if ($exception instanceof NotFoundHttpException){
            return $this->errorResponse('The specified URL cannot be found',404);
        }
        // post ,get ,.....
        if ($exception instanceof MethodNotAllowedHttpException){
            return $this->errorResponse('The specified method for the requests is invalid.',404);
        }



        // PermissionDoesNotExist
        if ($exception instanceof PermissionDoesNotExist){
            return $this->errorResponse($exception->getMessage(),500);
        }

        // RoleDoesNotExist
        if ($exception instanceof RoleDoesNotExist){
            return $this->errorResponse($exception->getMessage(),500);
        }

        // PermissionDoesNotExist
        if ($exception instanceof UnauthorizedException){
            return $this->errorResponse($exception->getMessage(),403);
        }




        //sql
        if ($exception instanceof QueryException){
            $errorCode = $exception->errorInfo ? $exception->errorInfo[1] : 0;
            // related row by id

            if($errorCode ==1451){
                return $this->errorResponse('Cannot remove this resource permanently. It is related with any other resource',409);
            }

            if($errorCode ==1062){
                return $this->errorResponse('Error Unique',409);
            }

            if($exception->getCode() === 2002){
                return $this->errorResponse('DataBase is Down!.',500);
            }

        }


        //all other http
        if ($exception instanceof HttpException){
            return $this->errorResponse($exception->getMessage(),$exception->getCode());
        }


        if(config('app.debug')){
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected Exception. Try later',500);

    }
}
