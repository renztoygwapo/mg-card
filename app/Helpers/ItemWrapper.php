<?php

namespace App\Helpers;


class ItemWrapper
{
    private $name;
    private $cost;

    public function __construct($name = '', $cost = '')
    {
        $this->name = $name;
        $this->cost = $cost;
    }

    public function __toString()
    {   
        $rightCols = 0;
        $leftCols = 20;
        $left = str_pad($this->name, $leftCols);
        $right = str_pad($this->cost, $rightCols, ' ', STR_PAD_LEFT);
        return "$left$right\n";
    }
}