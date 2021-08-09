<?php

namespace App;
use \Exception;

/**
 * Helper de Trilateración para challenge de Mercado Libre, escrito desde cero.
 * Basado en https://confluence.slac.stanford.edu/display/IEPM/TULIP+Algorithm+Alternative+Trilateration+Method
 */
class TrilaterationHelper{
    static function getKenobiPosition(){
        return new Position(-500,-200);
    }

    static function getSkywalkerPosition(){
        return new Position(100,-100);
    }
    
    static function getSatoPosition(){
        return new Position(500,100);
    }
    
    static function getTrilateratedPosition($d1, $d2, $d3) {
        $p1 = self::getKenobiPosition();
        $p2 = self::getSkywalkerPosition();
        $p3 = self::getSatoPosition();

        $precision = 2;

        // CALCULAR EL VALOR DE x
        // ======================

        // Forma polinomial
        // d . x = c1 . v1 - c2 . v2

        // Cálculo de coeficientes
        // -----------------------
        // c1 = 2 . (y3 - y2)
        $c1 = 2 * ($p3->y - $p2->y); 
        // c1 = 2 . (y2 - y1)
        $c2 = 2 * ($p2->y - $p1->y);

        // Cálculo de variables
        // --------------------
        // v1 = d1² - d2² - x1² - y1² + x2² + y2²
        $v1 = pow($d1,2) - pow($d2,2) - pow($p1->x,2) - pow($p1->y,2) + pow($p2->x,2) + pow($p2->y,2);
        // v2 = d2² - d3² - x2² - y2² + x3² + y3²
        $v2 = pow($d2,2) - pow($d3,2) - pow($p2->x,2) - pow($p2->y,2) + pow($p3->x,2) + pow($p3->y,2);
        
        // Cálculo de coeficiente de x
        // ---------------------------
        // d = 4 . [ ( x2 - x3 ) . ( y2 - y1 ) - ( x1 - x2 ) . ( y3 - y2 )]
        $d = 4 * (($p2->x - $p3->x) * ($p2->y - $p1->y) - ($p1->x - $p2->x) * ($p3->y - $p2->y));

        if(0 == $d){
            throw new Exception('Error, división por cero en el cálculo de x.');
        }

        // Cálculo de x
        // ------------
        // x = (c1 . v1 - c2 . v2) / d
        $x = round(($c1 * $v1 - $c2 * $v2) / $d, $precision);

        // CALCULAR EL VALOR DE y
        // ======================
        // Forma polinomial
        // f . y = e . x + i

        // Cálculo de coeficiente de x
        // ---------------------------
        // e = 2 . (x1 - x2)
        $e = 2 * ($p1->x - $p2->x);

        // Cálculo de término independiente
        // --------------------------------
        // i = d1² - d2² - x1² - y1² + x2² + y2²
        $i = pow($d1, 2) - pow($d2, 2) - pow($p1->x, 2) - pow($p1->y, 2) + pow($p2->x,2) + pow($p2->y,2);

        // Cálculo de coeficiente de y
        // ---------------------------
        // f = 2 . (y2 - y1)
        $f = 2 * ($p2->y - $p1->y);

        if(0 == $f){
            throw new Exception('Error, división por cero en el cálculo de y.');
        }

        // Cálculo de y
        // ------------
        // y = (e . x + i) / f

        $y = round(($e * $x + $i) / $f, $precision);

        return new Position($x, $y);
      }
}