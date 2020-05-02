<?php

return array(
    
    'path' => base_path('app/Modules'),
    'file_config' => 'modules.json',
    'module_config_name' => 'module.json',
    'module_namespace' => 'App\Modules\\',

    // default page
    'default_page' => true,
    'default_page_prefix' => 'module', // Ex : http://domain.com/module/,
    'default_page_middleware' => [], // Ex : ['auth', 'role:admin;]
);