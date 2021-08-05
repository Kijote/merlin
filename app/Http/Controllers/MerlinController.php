<?php

namespace App\Http\Controllers;

use App\TrilaterationHelper;
use Illuminate\Http\Request;
use Illuminate\Exceptions\ErrorException;

class MerlinController extends Controller
{   
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {}

    public function getPositionAndMessage(Request $request){
        $input = $request->all();
        list($sat1, $sat2, $sat3) = $this->key($input, 'satellites');;

        $d1 = $this->key($sat1, 'distance');
        $d2 = $this->key($sat2, 'distance');
        $d3 = $this->key($sat3, 'distance');

        $m1 = $this->key($sat1, 'message');
        $m2 = $this->key($sat2, 'message');
        $m3 = $this->key($sat3, 'message');

        try {
            $position = $this->getLocation($d1, $d2, $d3);
        } catch (Exception $e){
            $position = $e->getMessage();
        }

        try {
            $message = $this->getMessage($m1, $m2, $m3);
        } catch (Exception $e){
            $message = $e->getMessage();
        }


        $result = [
            'position' => $position,
            'message'  => $message
        ];

        return response()->json($result);
    }

    // input: distancia al emisor tal cual se recibe en cada satélite
    // output: las coordenadas ‘x’ e ‘y’ del emisor del mensaje
    private function getLocation(float ...$distances): array {
        // distancias (radios de circunferencias)
        list($d1, $d2, $d3) = $distances;
        return TrilaterationHelper::getTrilateratedPosition($d1, $d2, $d3)->asArray();
    }

    // input: el mensaje tal cual es recibido en cada satélite
    // output: el mensaje tal cual lo genera el emisor del mensaje
    private function getMessage(array ...$messages): string {
        // Armo un stack para los mensajes
        $stack = [];

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

    // Obtener el valor de una clave de cualquier nivel dentro del array
    private function key($array, ...$key) {
        $arr = $array;
        foreach ($key as $k){
            if(!isset($arr[$k])){
                return null;
            }
            $arr = $arr[$k];
        }
        return $arr;
    }

}
