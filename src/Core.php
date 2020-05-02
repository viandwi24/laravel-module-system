<?php

namespace Viandwi24\ModuleSystem;

use Viandwi24\ModuleSystem\Exceptions\ModuleException;
use Viandwi24\ModuleSystem\Facades\Module;

class Core
{
    protected  $path = '';
    protected  $file_config = '';

    /**
     * construct
     * 
     */
    public function __construct($config)
    {
        $this->path = $config['path'];
        $this->file_config = $this->path . '/' . $config['file_config'];
    }

    /**
     * Register a module
     *
     * @return void
     */
    public function register()
    {
        // check folder modules and config files
        $this->checkDir();

        // indexing a module
        $this->indexModule(true);

        // register module on load
        $this->registerModule();
    }


    /**
     * Check Directory of Modules
     * 
     */
    protected function checkDir()
    {
        // check folder modules
        if (!is_dir($this->path))
        {
            mkdir($this->path);
        }

        // check modules config file
        if (!is_file($this->file_config))
        {
            $config = [ 'list' => [], 'load' => [], ];
            file_put_contents($this->file_config, json_encode($config, JSON_PRETTY_PRINT));
        }
    }


    /**
     * Index a module
     * 
     * @return string
     */
    public function indexModule($saveToConfig = false)
    {
        // scan
        $scanned_dir = array_map(function($dir) {
            return basename($dir);
        }, glob($this->path . '/*', GLOB_ONLYDIR));
        
        // save to config file
        if ($saveToConfig)
        {
            $config = Module::getAppConfig();
            $config['list'] = $scanned_dir;
            file_put_contents($this->file_config, json_encode($config, JSON_PRETTY_PRINT));
        }

        // return
        return $scanned_dir;
    }



    /**
     * Register a Module
     */
    protected function registerModule()
    {
        $config = app()->make('module.config');
        $modules = Module::getLoad();
        foreach($modules as $module)
        {
            if (Module::checkConfig($module))
            {
                // get config module
                $module_config = Module::getConfig($module);

                // cehck service of module
                if (!isset($module_config['service'])) throw new ModuleException('Module "' . $module . '" does not have a registered service.');

                // check namespace of module
                if (!isset($module_config['namespace'])) throw new ModuleException('Module "' . $module . '" does not have a registered namespace.');

                // run service of module - register
                $namespace  = $config['module_namespace'] . $module_config['namespace'];
                $service =  $namespace . '\\' . $module_config['service'];
                app()->register($service);
                Module::setModuleLoaded($module, $service);
            }
        }
    }
}