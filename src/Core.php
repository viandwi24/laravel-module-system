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
        $modules = Module::getLoad();
        foreach($modules as $module)
        {
            if (Module::checkConfig($module))
            {
                // get service provider class
                $service = Module::getServiceProvider($module);

                // bind service
                app()->bind($service, function () use ($service) {
                    return new $service(app());
                });

                // register service
                app()->make($service)->register();
            }
        }
    }


    /**
     * Checking a module
     */
    public function check()
    {
        $modules = Module::getLoad();
        foreach($modules as $module)
        {
            // get service provider class
            $service = Module::getServiceProvider($module);

            // check service
            $check = app()->make($service)->check();

            // check a return value
            if (!is_array($check)) throw new ModuleException('Module "' . $module . '" on check() method not return array. in ('. $service .')');

            // check a return value must have a 'state'
            if (!isset($check['state'])) throw new ModuleException('Module "' . $module . '" on check() method not return "state" value. in ('. $service .')');

            // add
            Module::setModuleChecked($module, $service, $check);
        }
    }


    /**
     * Booting a module
     */
    public function boot()
    {
        $modules = Module::getModuleChecked();
        foreach($modules as $module)
        {
            // get service provider class
            $service = Module::getServiceProvider($module);

            // boot service
            $boot = app()->make($service)->boot();

            // add
            Module::setModuleLoaded($module, $service);
        }
    }




    /**
     * get a version of core
     * 
     * @return mixed
     */

    public function getVersion()
    {
        return "1.0.2";
    }
}