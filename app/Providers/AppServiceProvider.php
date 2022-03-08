<?php

namespace App\Providers;

use App\Interfaces\PostServiceInterface;
use App\Models\Post;
use App\Services\PostService;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //define the class to bind to the interface
        $this->app->bind(PostServiceInterface::class, function($app)
        {
             return new PostService(new Post());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {


    }
}
