<?php

namespace App\Http\Controllers;

use App\TrilaterationHelper;
use App\MessageHelper;
use Illuminate\Http\Request;
use Illuminate\Exceptions\ErrorException;
use \Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;


class MerlinController extends Controller
{
    // Obtiene la posición y el mensaje desde la información provista vía request
    public function getPositionAndMessage(Request $request){
        $input = $request->all();
        list($sat1, $sat2, $sat3) = $this->key($input, 'satellites');

        $d1 = $this->key($sat1, 'distance');
        $d2 = $this->key($sat2, 'distance');
        $d3 = $this->key($sat3, 'distance');

        $m1 = $this->key($sat1, 'message');
        $m2 = $this->key($sat2, 'message');
        $m3 = $this->key($sat3, 'message');

        $position = $this->validatePosition($d1, $d2, $d3);
        $message = $this->validateMessage($m1, $m2, $m3);

        return $this->returnJson($message, $position);
    }

    // Almacena la información parcial del satélite enviada por request
    public function setSplittedData(Request $request, $satelliteName){
        $input = $request->all();
        $distance = $this->key($input, 'distance');
        $message = $this->key($input, 'message');
        $satelliteName = !empty($satelliteName) ? $satelliteName : null;

        if(!is_null($satelliteName) && !is_null($distance) && !is_null($message)){
            app('db')->insert("INSERT INTO `satellite_data` (`name`,`request_time`,`distance`, `message`) VALUES 
            (?, NOW(), ?, ?)", [$satelliteName, $distance, json_encode($message)]);
        } else {
            throw new HttpException(404, 'Los datos no están completos.');
        }
    }

    // Obtiene la información de posición y mensaje desde los fragmenos almacenados
    public function getFromSplittedData(){
        /*
            Obtengo los 10 últimos registros que cumplen con la condición que se encuentren
            dentro de una ventana de tiempo de 150 segundos desde el último registro
        */
        $timeWindow = 150;
        $query = "SELECT `id`, `name`, `distance`, `message`, `request_time`
        FROM `satellite_data` WHERE UNIX_TIMESTAMP(`request_time`) > (SELECT UNIX_TIMESTAMP(`request_time`)
        FROM `satellite_data` ORDER BY `id` DESC LIMIT 1) - $timeWindow ORDER BY `id` DESC LIMIT 10";

        $regs = app('db')->select($query);
        
        // Si hay menos registros que la cantidad de satélites, 
        // no se podrá obtener la información
        if(count($regs)<3){
            throw new HttpException(404, 'Error, no hay suficiente información');
        }

        $skywalker = [];
        $kenobi = [];
        $sato = [];
        $i = 0;
        
        // Obtengo los registros mientras hayan registros
        // y estén vacíos los datos de por lo menos un satélite
        do {
            ${$regs[$i]->name} = [  'distance'  => $regs[$i]->distance, 
                                    'message'   => json_decode($regs[$i]->message)];
            $i++;
        } while ($i < count($regs) && (empty($skywalker) || empty($kenobi) || empty($sato)));

        // si existen datos para todos los satélites, obtener la posición
        if(isset($skywalker['distance']) && isset($kenobi['distance']) && isset($sato['distance'])){
            $position = $this->validatePosition( $kenobi['distance'], 
                                                $skywalker['distance'],
                                                $sato['distance']
                                            );
            
        } else {
            throw new HttpException(404, 'Error, no hay suficiente información');
        }

        if(isset($skywalker['message']) && isset($kenobi['message']) && isset($sato['message'])){
            $message = $this->validateMessage(   $kenobi['message'],
                                            $skywalker['message'],
                                            $sato['message']
                                        );
        } else {
            throw new HttpException(404, 'Error, no hay suficiente información');
        }

        return $this->returnJson($message, $position);
    }

    // Validar los datos de posición
    private function validatePosition($d1, $d2, $d3){
        $position = null;
        if(!is_null($d1) && is_numeric($d1) &&
           !is_null($d2) && is_numeric($d2) &&
           !is_null($d3) && is_numeric($d3)){
            try {
                $position = $this->getLocation((float) $d1, (float) $d2, (float) $d3);
            } catch (Exception $e){
                throw new HttpException(404, 'Error, no se puede determinar la posición');
            }
        } else {
            $position = false;
        }
        return $position;
    }

    // validar los datos de mensaje
    private function validateMessage($m1, $m2, $m3){
        $message = null;
        if(!is_null($m1) && !is_null($m2) && !is_null($m3)){
            try {
                $message = $this->getMessage((array) $m1, (array) $m2, (array) $m3);
            } catch (Exception $e){
                throw new HttpException(404, 'Error, no se puede determinar el mensaje');
            }
        }
        return $message;
    }

    // devuelve el mensaje y la posición en un json
    // verifica, además, que los resultados sean válidos
    private function returnJson($message, $position){
        if($message === false || $position === false){
            throw new HttpException(404, 'Error, no se puede determinar la información');
        }

        $result = [];
        if(!is_null($position)){
            $result['position'] = $position;
        }

        if(!is_null($message)){
            $result['message'] = $message;
        }

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
        // mensajes
        list($m1, $m2, $m3) = $messages;
        return MessageHelper::getMessage($m1, $m2, $m3);
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