<?php

require 'vendor/autoload.php';

define('URL_RASTREAR', 'http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do');
define('URL_COTIZAR', 'http://herramientascs.estafeta.com/Cotizador/Cotizar');

use Ivansabik\DomHunter\DomHunter;
use Ivansabik\DomHunter\Tabla;
use Ivansabik\DomHunter\KeyValue;
use Ivansabik\DomHunter\IdUnico;
use Ivansabik\DomHunter\NodoDom;

class Estafeta {

    public function rastreo($numero = NULL) {
        if ($numero) {
            $tipo_numero = $this->valida_numero($numero);

            # Params iniciales
            $params_peticion = array(
                "idioma" => 'es',
                "dispatch" => 'doRastreoInternet',
                'guias' => $numero
            );
            if ($tipo_numero == 'guia') {
                $params_peticion['tipoGuia'] = 'ESTAFETA';
            } elseif ($tipo_numero == 'rastreo') {
                $params_peticion['tipoGuia'] = 'REFERENCE';
            } elseif ($tipo_numero == 'invalido') {
                return array('mensaje_error' => 'No es un numero de guia o codigo de rastreo valido', 'error' => 2);
            }

            # Busca dom
            $hunter = new DomHunter(URL_RASTREAR, 1);
            $hunter->arrParamsPeticion = $params_peticion;
            $presas = array();
            $presas[] = array('numero_guia', new KeyValue('numero de guia'));
            $presas[] = array('codigo_rastreo', new KeyValue('digo de rastreo'));
            $presas[] = array('origen', new KeyValue('origen'));
            $presas[] = array('destino', new KeyValue('destino', TRUE, TRUE));
            $presas[] = array('cp_destino', new IdUnico(5, 'num'));
            $presas[] = array('servicio', new KeyValue('entrega garantizada', FALSE));
            $presas[] = array('estatus', new NodoDom(array('find' => '.respuestasazul'), 'plaintext', 1));
            $presas[] = array('fecha_recoleccion', new KeyValue('fecha de recoleccion'));
            $presas[] = array('fecha_programada', new KeyValue('de entrega', TRUE, TRUE));
            $presas[] = array('fecha_entrega', new KeyValue('Fecha y hora de entrega'));
            $presas[] = array('tipo_envio', new KeyValue('tipo de envio'));
            $presas[] = array('peso', new KeyValue('Peso kg'));
            $presas[] = array('peso_vol', new KeyValue('Peso volumétrico kg'));
            $presas[] = array('recibio', new KeyValue('recibi'));
            $presas[] = array('dimensiones', new KeyValue('Dimensiones cm'));
            $columnas = array('fecha', 'lugar_movimiento', 'comentarios');
            $presas[] = array('movimientos', new Tabla(array('ocurrencia' => -1), $columnas, 3));
            $hunter->arrPresas = $presas;
            $hunted = $hunter->hunt();
            if (strpos($hunted['estatus'], 'No hay informaci') !== false) {
                return array('estatus' => $hunted['estatus']);
            } else {
                # Descomposicion de dimensiones, de string a ancho, alto, largo
                $dimensiones = $hunted['dimensiones'];
                $piezas_dimensiones = explode('x', $dimensiones);
                $ancho = $piezas_dimensiones[0];
                $alto = $piezas_dimensiones[1];
                $largo = $piezas_dimensiones[2];
                $hunted['dimensiones'] = array('ancho' => $ancho, 'alto' => $alto, 'largo' => $largo);

                # Construye historial de movimientos
                $historial = $hunted['movimientos'];
                $movimientos = array();
                foreach ($historial as $evento) {
                    $movimiento = array();
                    $movimiento['descripcion'] = $evento['lugar_movimiento'];
                    $movimiento['fecha'] = $evento['fecha'];
                    # Comentado por buggiento https://github.com/ivanrodriguez/dom-hunter/issues/4
                    # movimiento['comentarios'] = $evento['comentarios'];
                    # En proceso de entrega
                    if (preg_match('/\bPROCESO DE ENTREGA\b/i', $evento['lugar_movimiento'])) {
                        $movimiento['id'] = 1;
                    }
                    # Llegada a CEDI
                    if (preg_match('/\bLLEGADA A CENTRO DE DISTRIBUCI\b/i', $evento['lugar_movimiento'])) {
                        $movimiento['id'] = 2;
                    }
                    # En ruta foránea  hacia un destino
                    if (preg_match('/\bEN RUTA FOR\b/i', $evento['lugar_movimiento'])) {
                        $movimiento['id'] = 3;
                    }
                    # Recolección en oficina por ruta local
                    if (preg_match('/\bN EN OFICINA\b/i', $evento['lugar_movimiento'])) {
                        $movimiento['id'] = 4;
                    }
                    # Recibido en oficina
                    if (preg_match('/\bRECIBIDO EN OFICINA\b/i', $evento['lugar_movimiento'])) {
                        $movimiento['id'] = 5;
                    }
                    # Movimiento en CEDI
                    if (preg_match('/\bMOVIMIENTO EN CENTRO DE DISTRIBUCI\b/i', $evento['lugar_movimiento'])) {
                        $movimiento['id'] = 6;
                    }
                    # Aclaracion en proceso
                    if (preg_match('/\bN EN PROCESO\b/i', $evento['lugar_movimiento'])) {
                        $movimiento['id'] = 7;
                    }
                    if ($evento['lugar_movimiento']) {
                        $movimientos[] = $movimiento;
                    }
                }
                $hunted['movimientos'] = $movimientos;
                return $hunted;
            }
        } else {
            return array('mensaje_error' => 'Falta el parametro numero', 'error' => 1);
        }
    }

    private function valida_numero($numero) {
        # Numero de guia (22 y alfanumérico)
        if (strlen($numero) == 22 && ctype_alnum($numero)) {
            return 'guia';
        }
        # Codigo de rastreo (10 y numérico)
        if (strlen($_GET['numero']) == 10 && is_numeric($numero)) {
            return 'rastreo';
        }
        return 'invalido';
    }

}

?>