# Laravel Module System

Add Module System to Your Laravel Application, with # Modular Application Architecture.  The concept is simple, pull out the default service provider laravel and then reprogramming it to be loaded again and controlled like a Module / Plugin.

> *Specification*
> * Core Version : 1.0.5
> * Laravel Support : 7.x

# Installation
Add to your project with composer :
```
composer require viandwi24/laravel-module-system
```

Add service provider to your `config/app.php`
```
Viandwi24\ModuleSystem\ServiceProvider::class,
```

Add service provider to your `config/app.php`
```
'Module'  => Viandwi24\ModuleSystem\Facades\Module::class
```

and, you can publish config 
```
php artisan vendor:publish --provider="Viandwi24\ModuleSystem\ServiceProvider"
```


# Usage
## Default Page 
We have a default page for control and management your module, Default Page can your access in `http://localhost:8000/module`. You can change default page url or disable this page with a change a config.

Config in `config/module.php` :
```
'default_page' => true,
'default_page_prefix' => 'module',
'default_page_middleware' => [], 
```


Run Laravel 
```
php artisan serve
```

You can access this url on browser 
```
http://localhost:8000/module
```
![Preview](https://i.ibb.co/xhYXWnw/Screenshot-from-2020-05-02-09-56-33.png)

For Example, you can download example module in
[this link](https://github.com/viandwi24/laravel-module-system/raw/master/examples/ExampleModule.zip)
Download Example Module, and then goto `http://localhost:8000/module` and click "Install .zip", upload examplemodule.zip
Click, Activate in List Module 

![Preview](https://i.ibb.co/zrh4TN3/Screenshot-from-2020-05-02-10-00-32.png)

Finally, goto `http://localhost:8000/tes` and you see this in browser

![Preview](https://i.ibb.co/020Jz2H/Screenshot-from-2020-05-02-10-02-04.png)

Yeah, route `http://localhost:8000/tes` is a dynamic route generated from `ExampleModule`, if you disable this plugin, then when you re-access this route you will see page 404. 


## Module Facade
Module class menyediakan hampir keseluruhan ungsi module, anda bisa menggunakanya dengan menggunakan :
```
Viandwi24\ModuleSystem\Facades\Module
```

### Get List Module
```
Module::get();
```
### Enable a Module
```
Module::enable($module_name);
```
### Disable a Module
```
Module::disable($module_name);
```

# Make Module
Module merupakan inspirasi dari Service Provider, Module yang ada memanfaatkan Service Provider bawaan laravel yang mudah anda ketahui.
```
php artisan make:module ExampleModule
```


# Structure Module
Module mempunyai struktur sederhana, folder Module default berada pada `app/Modules`. dimana di sebagai contoh kita telah membuat module baru `ExampleModule` di `app/Modules/ExampleModule` dengan menggunakan perintah :
```
php artisan make:module ExampleModule
```

Isi File :
* module.js
* ExampleModuleServiceProvider

## modules.js
Merupakan file konfigurasi utama sebuah module, berisi informasi :
```
{
    "name"          : "ExampleModule",
    "description"   : "Description your module",
    "version"       : "1.0",
    "author"        : "viandwi24",
    "email"         : "fiandwi0424@gmail.com",
    "web"           : "viandwi24.github.io",
    "namespace"     : "ExampleModule",
    "service"       : "ExampleModuleServiceProvider"
}
```
* namespace : namespace utama untuk module, jika dalam contoh maka namespace tersebut nantinya akan di parse menjadi `App\Modules\ExampleModule`
* service : Class utama module yang akan dieksekusi, mengikut cara kerja Service Provider bawaan laravel yang dimodifikasi.

##  Service Class - ExampleModuleServiceProvider.php
File ini dieksekusi layaknya Service Provider pada Laravel, berikut struktur utamanya
```
<?php
namespace App\Modules\ExampleModule;

use Viandwi24\ModuleSystem\Base\Service;
use Viandwi24\ModuleSystem\Interfaces\ModuleInterface;

class ExampleModuleServiceProvider extends Service implements ModuleInterface
{

    public function register()
    {
        //
    }

    public function boot()
    {
        //
    }

    public function check()
    {
        return [
            'state' => 'ready'
        ];
    }
}
```

* register : fungsi yang akan dijalankan ketika laravel bootstraping
* boot : fungsi yang akan dijalakan ketika semua bootstrap laravel selesai
* check : fungsi ini di jalankan ketika setelah semua module lainya di register. ini berfungsi mengembalikan state apakah Plugin siap di load, ata belum atau mungkin ingin menampilkan suatu error.

### check() method
fungsi ini berfungsi untuk mengembalikan nilai array state.
#### Ready
State Ready akan membuat Module akan di booting
```
['state' => 'ready']
```
#### Not Ready
State Not Ready akan membuat Module tetap di booting tetapi akan menampilkan peringatan bahwa Module belum siap dan butuh di setup. akan menampilkan opsi setup, nilai setup adalah berisi link untuk melakukan konfigurasi module agar bisa menjadi Ready nantinya
```
[
    'state' => 'not_ready',
    'setup' => route('my_module_route.setup')
]
```

#### Error
State Error akan membuat Module tidak akan di load dan mengeluarkan error peringatan di Module Management nantinya.
```
[
    'state' => 'error',
    'error' => 'Tidak support dengan versi laravel anda!'
]
```