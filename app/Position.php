<?php

namespace App;
/**
 * Clase para crear objetos Position con propiedades x e y
 */
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
