<?php
namespace Viandwi24\ModuleSystem\Base;

use Illuminate\Support\ServiceProvider;
use Viandwi24\ModuleSystem\Facades\Module;

class Service extends ServiceProvider
{
    protected function path()
    {
        $config = app()->make('module.config');
        $class = str_replace($config['module_namespace'], '', get_called_class());
        $module_path = $config['path'];
        $result = str_replace('\\', '/', $module_path . $class);
        return dirname($result); 
    }

    protected function config()
    {
        $config = app()->make('module.config');
        $class = str_replace($config['module_namespace'], '', get_called_class());
        $module_name = explode('\\', basename($class))[0];
        return Module::getConfig($module_name);
    }
}