<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Exception;
use Illuminate\Support\Facades\Request;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        app('api.exception')->register(function (Exception $exception) {
            $request = Request::capture();
            return app('App\Exceptions\Handler')->render($request, $exception);
        });
    }
}
