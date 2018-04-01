<?php

namespace App\Providers;

use App\Model\DataSource\ProductCrawler;
use App\Model\DataSource\ProductListDataSourceInterface;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production')
        {
            $this->app->register(IdeHelperServiceProvider::class);
        }
        $this->app->bind(ProductListDataSourceInterface::class, function ($app)
        {
            return new ProductCrawler(new \Requests_Session());
        });
    }
}
