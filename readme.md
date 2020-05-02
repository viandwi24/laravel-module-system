# Laravel Module System

Add Module System to Your Laravel Application, with # Modular Application Architecture.  The concept is simple, pull out the default service provider laravel and then reprogramming it to be loaded again and controlled like a Module / Plugin.
`Core Version : 1.0.3`


# Installation
add to your project with composer :

    composer require viandwi24/laravel-module-system
add service provider to your `config/app.php`

    Viandwi24\ModuleSystem\ServiceProvider::class,
add to aliases in `config/app.php`

    'Module'  => Viandwi24\ModuleSystem\Facades\Module::class
and, you cant publish config 

    php artisan vendor:publish --provider="Viandwi24\ModuleSystem\ServiceProvider"       

# Usage
Run Laravel 

    php artisan serve

You can access this url on browser 

    http://localhost:8000/module
![Preview](https://i.ibb.co/xhYXWnw/Screenshot-from-2020-05-02-09-56-33.png)

For Example, you can download example module in [this link](github.com)
Download Example Module, and then goto `http://localhost:8000/module` and click "Install .zip", upload examplemodule.zip
Click, Activate in List Module 
![Preview](https://i.ibb.co/zrh4TN3/Screenshot-from-2020-05-02-10-00-32.png)

Finally, goto `http://localhost:8000/tes` and you see this in browser
![Preview](https://i.ibb.co/020Jz2H/Screenshot-from-2020-05-02-10-02-04.png)

Yeah, route `http://localhost:8000/tes` is a dynamic route generated from `ExampleModule`, if you modify the plugin, then when you re-access this route you will see page 404.

# Structure
Module yang dibuat akan berada pada `app/Modules` yang bisa diubah di `config/module.php`.
Folder `Modules` didalamnya akan berisi folder folder module yang terpasang.
Module yang ada dibuat berdasarkan ServiceProvider bawaan Laravel, dimanamemiliki fungsi utama Register dan Boot.
Untuk Membuat Module baru,anda harus membuat folder baru untuk plugin anda dan membuat file bernama `module.json` didalam folder plugin kalian yang berisi :

    {
    	"name"			:  "Example Module",
    	"description"	:  "a Example Module",
    	"version"		:  "1.0",
    	"author"		:  "viandwi24",
    	"email"			:  "fiandwi0424@gmail.com",
    	"web"			:  "https://www.github.com/viandw24/",
    	"namespace"		:  "ExampleModule",
    	"service"		:  "ExampleModuleServiceProvider"
    }`

 - name : merupakan nama module
 - description : deskripsikan module kalian
 - version : versi module
 - author : nama penulis
 - email : email penulis
 - web : web penulis
 - namespace : adalah Namespace yang harus digunakan disetiap module kalian, sebagai contih `ExampleModule` artinya disetiapfile yang ada harus menggunakan namespace `App\Modules\ExampleModule`
 -  service : file service provider yang akan diakses pertama kali

