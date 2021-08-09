<?php

namespace App;
use \Exception;

/**
 * Helper de Mensajes para challenge de Mercado Libre.
 */
class MessageHelper{
    
    static function getMessage($m1, $m2, $m3) {
        
        // Armo un stack para los mensajes
        $stack = [];

        // Array de mensajes
        $messages = [$m1, $m2, $m3];

        // Obtengo la menor cantidad de índices de los arrays pasados
        $count = min(array_map('count', $messages));

        // Elimino el posible "delay" entre los mensajes
        $messages = array_map(function ($item) use ($count) {
            return array_slice($item, count($item) - $count);
        }, $messages);

        // Itero por cada ítem de los arrays
        for($i = 0; $i < $count; $i++){
            $stack[] = array_reduce([$messages[0][$i], $messages[1][$i], $messages[2][$i]], function($carry, $item){ return !empty($carry) ? $carry : $item; });
        }

        // uno el stack con espacios y retorno el mensaje
        return implode(' ', $stack);
    }
}