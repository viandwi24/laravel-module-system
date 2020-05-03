<?php

namespace Viandwi24\ModuleSystem;

use Viandwi24\ModuleSystem\Exceptions\ModuleException;

class Module
{
    protected $path = '';
    protected $file_config = '';
    protected $module_config_name = '';
    protected $config = [];
    protected $modulesChecked = [];
    protected $modulesLoaded = [];
    protected $modulesRegister = [];

    /**
     * construct
     * 
     */
    public function __construct($config)
    {
        $this->path = $config['path'];
        $this->file_config = $this->path . '/' . $config['file_config'];
        $this->module_config_name = $config['module_config_name'];
        $this->config = $this->getAppConfig();
    }


    /**
     * Indexing a modules folder
     * 
     * @return array
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
            $config = $this->getAppConfig();
            $config['list'] = $scanned_dir;
            $config['list'] = (array) $config['list'];
            file_put_contents($this->file_config, json_encode($config, JSON_PRETTY_PRINT));
            $this->config = $this->getAppConfig();
        }

        // return
        return $scanned_dir;
    }

    /**
     * Get Load Module
     */
    public function getLoad()
    {
        return $this->config['load'];
    }


    /**
     * Load app config
     * 
     * @return array
     */
    public function getAppConfig()
    {
        $config = (array) json_decode(file_get_contents($this->file_config));
        return $config;
    }



    /**
     * check config module
     * 
     * @return boolean
     */
    public function checkConfig($module)
    {
        $path = $this->path . '/' . $module . '/' . $this->module_config_name;
        
        /**
         * check config file in module folder
         * Hint if error :
         * 
         * [+] Check config in this folder module
         * [+] If this module not found, try to disable in modules.json
         */
        if (!is_file($path)) throw new ModuleException('Config module "' . $module . '" not found. in : (' . $path . ')');
        return true;
    }


    /**
     * Load module config
     * 
     * @return array
     */
    public function getConfig($module)
    {
        $path = $this->path . '/' . $module . '/' . $this->module_config_name;
        $config = (array) json_decode(file_get_contents($path));
        return $config;
    }


    /**
     * Srt module register
     */
    public function setModuleRegister($module, $service)
    {
        $this->modulesRegister[] = [
            'name' => $module,
            'service' => $service,
        ];
    }


    /**
     * Set Module Loaded
     */
    public function setModuleLoaded($module, $service)
    {
        $this->modulesLoaded[] = [
            'name' => $module,
            'service' => $service,
            'info' => $this->getConfig($module),
            'state' => 'active'
        ];
    }


    /**
     * Set Module Checked
     */
    public function setModuleChecked($module, $service, $check)
    {
        $module_log = [
            'name' => $module,
            'service' => $service,
            'info' => $this->getConfig($module),
            'state' => $check['state']
        ];

        if ($check['state'] == 'error')
        {
            $module_log['error'] = @$check['error'];
        } else if ($check['state'] == 'not_ready')
        {
            $module_log['setup'] = @$check['setup'];
        }

        // if 
        if (isset($check['boot'])) $module_log['boot'] = @$check['boot'];


        $this->modulesChecked[] = $module_log;
    }


    /**
     * Get module checked
     */
    public function getModuleChecked($name = null)
    {
        $modules = $this->modulesChecked;
        $result = [];

        // get only state ready and not_ready
        foreach($modules as $module)
        {
            if ($module['state'] == 'ready' || $module['state'] == 'not_ready')
            {
                if ($name != null && $name == $module['name']) return $module;
                array_push($result, $module['name']);
            }
        }

        // return
        return $result;
    }


    /**
     * Module Has
     */
    public function has($module)
    {
        $search = false;
        $modules_loaded = $this->modulesRegister;
        foreach ($modules_loaded as $key => $val) {
            if ($val['name'] === $module) {
                $search = true;
                break;
            }
        }
        return $search;
    }


    /**
     * Get Module
     * 
     * @return array
     */
    public function get()
    {
        $modules = $this->config['list'];
        $modules_loaded = $this->modulesChecked;

        foreach($modules as $module)
        {
            $search = false;
            foreach ($modules_loaded as $key => $val) {
                if ($val['name'] === $module) {
                    $search = true;
                    break;
                }
            }


            $config = app()->make('module.config');
            if (!$search)
            {
                if ($this->checkConfig($module))
                {
                    // get config module
                    $module_config = Module::getConfig($module);
                    $this->checkConfigModule($module, $module_config);
                    
                    // defines service
                    $namespace  = $config['module_namespace'] . $module_config['namespace'];
                    $service =  $namespace . '\\' . $module_config['service'];
                    
                    // add
                    $modules_loaded[] = [
                        'name' => $module,
                        'service' => $service,
                        'info' => $this->getConfig($module),
                        'state' => 'disable'
                    ];
                }
            }
        }
        $result = (object) json_decode(json_encode($modules_loaded));
        return (array) $result;
    }


    /**
     * Get service provider a module
     * 
     */
    public function getServiceProvider($module)
    {
        // config
        $config = app()->make('module.config');

        // get config module
        $module_config = $this->getConfig($module);
        $this->checkConfigModule($module, $module_config);

        // defines service
        $namespace  = $config['module_namespace'] . $module_config['namespace'];
        $service =  $namespace . '\\' . $module_config['service'];
        return $service;
    }


    /**
     * Load a Module
     */
    public function checkConfigModule($module, $module_config)
    {
        // modules config system
        $config = app()->make('module.config');

        /**
         * check service of module
         * 
         * if error, Hint : 
         * [+] defines "service" in your config "module.json"
         * 
         */
        if (!isset($module_config['service'])) throw new ModuleException('Module "' . $module . '" does not have a registered service.');

        /**
         * check namespace of module
         * 
         * if error, Hint : 
         * [+] defines "namespace" in your config "module.json"
         * 
         */
        if (!isset($module_config['namespace'])) throw new ModuleException('Module "' . $module . '" does not have a registered namespace.');

        // defines service
        $namespace  = $config['module_namespace'] . $module_config['namespace'];
        $service =  $namespace . '\\' . $module_config['service'];

        /**
         * check class service is exist
         * 
         * if error, Hint : 
         * [+] check service class name
         * [+] check service namespace name
         * 
         */
        if (!class_exists($service)) throw new ModuleException('Module "' . $module . '", Service is not exists. in : ('. $service .')');
    }




    /**
     * Disable module
     * 
     */
    public function disable($module)
    {
        $config = $this->getAppConfig();
        
        // check module is register ?
        if (!in_array($module, $config['list'])) return false;

        // remove
        unset($config['load'][array_search( $module, $config['load'] )]);
        $config['load'] = (array) $config['load'];

        //
        return file_put_contents($this->file_config, json_encode($config, JSON_PRETTY_PRINT));
    }


    /**
     * Enable module
     * 
     */
    public function enable($module)
    {
        $config = $this->getAppConfig();
        
        // check module is register ?
        if (!in_array($module, $config['list'])) return false;

        //
        $config['load'] = (array) $config['load'];

        // add 
        if (!in_array($module, $config['load'])) $config['load'][] = $module;

        //
        return file_put_contents($this->file_config, json_encode($config, JSON_PRETTY_PRINT));
    }



    /**
     * Get Path
     */
    public function getPath()
    {
        return $this->path;
    }
}