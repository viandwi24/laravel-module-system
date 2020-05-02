<?php

use Illuminate\Support\Facades\Route;

// config
$config = app()->make('module.config');


// Route
Route::group([
    'prefix' => $config['default_page_prefix'],
    'middleware' => $config['default_page_middleware'],
    'namespace' => 'Viandwi24\\ModuleSystem\\Controllers'
], function () {
    Route::get('', 'ModuleController@index')->name('module');
    Route::get('enable/{module}', 'ModuleController@enable')->name('module.enable');
    Route::get('disable/{module}', 'ModuleController@disable')->name('module.disable');
    Route::post('install', 'ModuleController@install')->name('module.install');
});