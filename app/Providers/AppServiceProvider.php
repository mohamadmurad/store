<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //JsonResource::withoutWrapping();

        Passport::routes(null, [
            'prefix' => 'api/v1/oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ]);


        Passport::tokensExpireIn(Carbon::now()->addDays(10));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(10));
    }
}
