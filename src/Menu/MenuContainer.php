<?php

namespace Viandwi24\ModuleSystem\Menu;

use Adbar\Dot;

class MenuContainer
{
    protected $properties = [];
    protected $render = null;
    protected $renderSub = null;
    protected $item = [];
    
    /**
     * Constructor
     *
     * @param array $properties
     */
    public function __construct($properties, $render, $renderSub)
    {
        $this->properties = $properties;
        $this->render = $render;
        $this->renderSub = $renderSub;
    }

    /**
     * Add a item
     *
     * @param String $name
     * @param Mixed $items
     * @return void
     */
    public function add(String $name, $items)
    {
        $name_arr = explode('.', $name);
        $dot = new Dot($this->item);

        $tmp = '';
        $i = 0;
        foreach($name_arr as $item)
        {
            $tmp .= $item;

            // 
            if ($dot->get($tmp) == null)
            {
                $dot->set($tmp, $this->decProperties());
            }

            // 
            if ($i == count($name_arr) - 1)
            {
                $dot->set($tmp, $this->parseProperties($this->decProperties(), $items));
            }

            // 
            $tmp .= ".subitems.";
            $i++;
        }

        // 
        $this->item = $dot->get();

        // 
        return $this;
    }

    /**
     * Render
     *
     * @return void
     */
    public function render()
    {
        if ($this->render == null)
        {
            $this->render = function ($item, $isSubitem = false, $parent = []) {
                return "<li>" . $item['text'] . "</li>\n";
            };
        }

        if ($this->renderSub == null)
        {
            $this->renderSub = function ($render, $parent) {
                $result = "<li>\n<a>" . $parent['text'] . "</a>\n";
                $result .= "<ul>\n" . $render . "</ul>\n</li>\n";
                return $result;
            };
        }


        // render function
        // dd($this->item);
        $render = $this->render;
        $renderSub = $this->renderSub;
        $result = "<ul>\n";
        foreach($this->item as $item)
        {
            if (isset($item['subitems'])) 
            {
                $result .= $renderSub($this->recursiveRender($item['subitems'], true, $item), $item);
            } else {
                $result .= $render($item, false);
            }
        }
        $result .= '</ul>';
        return $result;
    }


    protected function recursiveRender($item, $isSubitem = false, $parent = [])
    {
        //  get closure
        $render = $this->render;
        $renderSub = $this->renderSub;
        $result = '';
        
        // 
        foreach($item as $sub)
        {
            if (isset($sub['subitems'])) 
            {
                $result .= $renderSub($this->recursiveRender($sub['subitems'], true, $sub), $sub);
            } else {
                $result .= $render($sub, true);
            }
        }

        // return
        return $result;
    }


    /**
     * ToString
     *
     * @return string
     */
    public function __toString()
    {
        return 'awe';
    }


    /**
     * toArray
     *
     * @return string
     */
    public function toArray()
    {
        return $this->item;
    }

    // 
    protected function decProperties()
    {
        $result = [];
        foreach($this->properties as $property)
        {
            $result[$property] = '';
        }
        return $result;
    }

    protected function parseProperties($decProperties, $items)
    {
        foreach ($items as $index => $item) {
            if (isset($decProperties[$index])) $decProperties[$index] = $item;
        }
        return $decProperties;
    }
}