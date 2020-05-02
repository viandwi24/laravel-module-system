<?php
namespace Viandwi24\ModuleSystem\Interfaces;

interface ModuleServiceProvider
{
    public function register();
    public function boot();
    public function check();
}