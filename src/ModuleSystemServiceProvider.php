<?php

namespace Viandwi24\ModuleSystem;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Viandwi24\ModuleSystem\Facades\Core;

class ModuleSystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // config
        $this->bindConfig();

        // bind
        $this->bindClass();

        // default page
        $this->defaultPage();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // register a modules
        Core::register();
    }



    protected function bindConfig()
    {
        $configPath = __DIR__ . '/../config/module.php';
        $this->mergeConfigFrom($configPath, 'module');

        $this->app->bind('module.config', function () {
            return $this->app['config']->get('module');
        });
    }


    // 
    protected function bindClass()
    {
        $this->app->bind('core',function() {
            $config = $this->app->make('module.config');
            return new \Viandwi24\ModuleSystem\Core($config);
        });
        $this->app->bind('module',function() {
            $config = $this->app->make('module.config');
            return new \Viandwi24\ModuleSystem\Module($config);
        });
    }


    /**
     * add Default Page For Control Module
     */
    protected function defaultPage()
    {
        $config = $this->app->make('module.config');
        // register view
        $this->loadViewsFrom(__DIR__ . '/Views', 'ModuleSystem');

        // register route
        Route::middleware('web')->group(__DIR__ . '/Routes/route.php');
    }
}
