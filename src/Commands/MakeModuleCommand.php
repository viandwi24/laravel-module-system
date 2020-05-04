<?php

namespace Viandwi24\ModuleSystem\Commands;

use Illuminate\Console\Command;
use Viandwi24\ModuleSystem\Facades\Module;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name : The Name of Module} {--f|full : Make full module with folder, controller, dll}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Modules';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // name and mame
        $namespace = str_replace(' ', '', $this->argument('name'));
        $name = $this->argument('name');
        $service = $namespace . "ServiceProvider";
        // return $this->info($namespace);

        // make a folder
        $path = Module::getPath() . '/' . $name;
        if (is_dir($path)) $this->error('Module is already exist!');
        mkdir($path);

        // make a module.json
        $options = app()->make('module.config');
        $config_module = [
            "name" => $name,
            "description" => "Description your module",
            "version" => "1.0",
            "author" => "viandwi24",
            "email" => "fiandwi0424@gmail.com",
            "web" => "viandwi24.github.io",
            "namespace" => $namespace,
            "service" => $service
        ];
        file_put_contents($path . '/' . $options['module_config_name'], json_encode($config_module, JSON_PRETTY_PRINT));

        // make a service
        $this->makeService($name, $namespace, $service, $path);

        // check full module generate?
        $full = $this->option('full');
        if ($full)
        {
            $this->makeController($name, $namespace, $path);
            $this->makeAdditional($name, $namespace, $path);
        }


        // success
        $this->info('Module "' . $name . '" Created in (' . $path . ')');
    }


    /**
     * Make service provider from stubs
     *
     * @param string $name
     * @param string $namespace
     * @param string $service
     * @param string $path
     * @return void
     */
    protected function makeService($name, $namespace, $service, $path)
    {
        $serviceTemplate = str_replace(
            [ '{{serviceName}}', '{{serviceNameSpace}}', ], 
            [ $name, $service ], 
            $this->stub('service')
        );
        $servicePath = $path . '/' . $service . '.php';
        file_put_contents($servicePath , $serviceTemplate);
    }



    /**
     * Make Controller from stub
     *
     * @param string $name
     * @param string $namespace
     * @param string $path
     * @return void
     */
    protected function makeController($name, $namespace, $path)
    {
        // make folder
        mkdir($path . '/Controllers');
        
        // make controller file
        $name = $name . 'Controller';
        $controllerTemplate = str_replace(
            [ '{{controllerName}}', '{{controllerNamespace}}', ], 
            [ $name, $namespace ], 
            $this->stub('controller')
        );
        $controllerPath = $path . '/Controllers//' . $name . '.php';
        file_put_contents($controllerPath , $controllerTemplate);
    }



    protected function makeAdditional($name, $namespace, $path)
    {
        // make views
        mkdir($path . '/views');

        // make migrations
        mkdir($path . '/migrations');

        // make models
        mkdir($path . '/Models');


        // make routes
        $routesTemplate = str_replace(
            [ '{{moduleName}}', '{{moduleNameLower}}' ], 
            [ $name, strtolower($name) ], 
            $this->stub('routes')
        );
        $routesPath = $path . '/routes' . '.php';
        file_put_contents($routesPath , $routesTemplate);
    }



    /**
     * retrieve a stub
     *
     * @param string $name
     * @return void
     */
    protected function stub($name)
    {
        return file_get_contents(__DIR__ . '/../../stubs/'. $name .'.stub');
    }
}
