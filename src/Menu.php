<?php

namespace Viandwi24\ModuleSystem;

use Closure;
use Viandwi24\ModuleSystem\Menu\MenuContainer;

class Menu
{
    protected $menu = [];

    public function __construct()
    {
        // 
    }

    public function make(string $menu, array $properties, Closure $render = null, Closure $renderSub = null)
    {
        $this->menu[$menu] = new MenuContainer($properties, $render, $renderSub);
        return $this->menu[$menu];
    }

    public function get($menu)
    {
        if (!isset($this->menu[$menu])) return null;
        return $this->menu[$menu];
    }
}