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
        // check config file in module folder
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


        $this->modulesChecked[] = $module_log;
    }


    /**
     * Get module checked
     */
    public function getModuleChecked()
    {
        $modules = $this->modulesChecked;
        $result = [];

        // get only state ready and not_ready
        foreach($modules as $module)
        {
            if ($module['state'] == 'ready' || $module['state'] == 'not_ready')
            {
                array_push($result, $module['name']);
            }
        }

        // return
        return $result;
    }


    /**
     * Get Module
     * 
     * @return array
     */
    public function get()
    {
        $modules = $this->config['list'];
        $modules_loaded = $this->modulesLoaded;

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
        return (object) json_decode(json_encode($modules_loaded));
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
        unset($config['load'][array_search( $module, $config['load'] )]);
        return file_put_contents($this->file_config, json_encode($config, JSON_PRETTY_PRINT));
    }


    /**
     * Enable module
     * 
     */
    public function enable($module)
    {
        $config = $this->getAppConfig();
        if (!in_array($module, $config['load'])) $config['load'][] = $module;
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