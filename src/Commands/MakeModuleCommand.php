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
    protected $signature = 'make:module {name : The Name of Module}';

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
        $this->info('Module "' . $name . '" Created in (' . $path . ')');
    }


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

    protected function stub($name)
    {
        return file_get_contents(__DIR__ . '/../../stubs/'. $name .'.stubs');
    }
}
