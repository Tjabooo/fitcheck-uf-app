<?php

namespace App\Providers;

use App\Models\ClothingArticle;
use App\Policies\ClothingArticlePolicy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        ClothingArticle::class => ClothingArticlePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind('path.public', function() {
            return base_path('public_html');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
