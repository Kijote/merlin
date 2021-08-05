<?php

namespace App;

class Position {
    public $x, $y;

    function __construct($x, $y){
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    function asArray(){
        return [$this->x, $this->y];
    }
}
