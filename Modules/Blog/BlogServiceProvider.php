<?php

namespace Modules\Blog;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        Route::prefix('api/v1')
            ->middleware('api')
            ->group(__DIR__.'/routes/api.php');
    }
}
