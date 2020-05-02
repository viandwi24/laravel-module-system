<?php

namespace Viandwi24\ModuleSystem\Facades;

class Module extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'module';
    }
}