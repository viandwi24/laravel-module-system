<?php

namespace Viandwi24\ModuleSystem;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Illuminate\Support\Facades\Route;
use Viandwi24\ModuleSystem\Facades\Core;
use Viandwi24\ModuleSystem\Commands\MakeModuleCommand;
use Viandwi24\ModuleSystem\Facades\Menu;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // publish
        $this->publishConfig();

        // config
        $this->bindConfig();

        // bind
        $this->bindClass();

        // default page
        $this->defaultPage();

        // register a module
        Core::register();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // make command
        $this->makeCustomCommandConsole();        
        
        // booted
        $this->app->booted(function () {
            // checking a module
            Core::check();

            // booting a module
            Core::boot();
        });
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
        $this->app->bind('viandwi24.modulesystem.menu',function() {
            return new \Viandwi24\ModuleSystem\Menu;
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

        // 
        if ($config['default_page'])
        {
            // register route
            Route::middleware('web')->group(__DIR__ . '/Routes/route.php');
        }
    }



    /**
     * Publish
     */
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__.'/../config/' => base_path('config'),
        ]);
    }



    /**
     * Make custom command
     * 
     */
    protected function makeCustomCommandConsole()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModuleCommand::class
            ]);
        }
    }
}
