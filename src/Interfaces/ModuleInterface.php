<?php
namespace Viandwi24\ModuleSystem\Interfaces;

interface ModuleInterface
{
    public function register();
    public function boot();
    public function check();
}