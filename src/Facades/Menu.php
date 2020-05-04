<?php

namespace Viandwi24\ModuleSystem\Facades;

class Menu extends \Illuminate\Support\Facades\Facade
{
    public static function getFacadeAccessor()
    {
        return 'viandwi24.modulesystem.menu';
    }
}