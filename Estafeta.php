<?php

require 'vendor/autoload.php';

define('URL_RASTREAR', 'http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do');
define('URL_COTIZAR', 'http://herramientascs.estafeta.com/Cotizador/Cotizar');
define('URL_FIRMA', 'http://rastreo3.estafeta.com');
define('URL_COMPROBANTE', 'http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do?dispatch=doComprobanteEntrega&guiaEst=');

use Ivansabik\DomHunter\DomHunter;
use Ivansabik\DomHunter\Tabla;
use Ivansabik\DomHunter\KeyValue;
use Ivansabik\DomHunter\IdUnico;
use Ivansabik\DomHunter\NodoDom;

class Estafeta {
    # Endpoint de /rastreo?numero=<NUMERO DE GUIA O CODIGO DE RASTREO>

    public function rastreo($numero = NULL) {
        if (!$numero) {
            return array('mensaje_error' => 'Falta el parametro "numero"', 'error' => 1);
        }
        $tipo_numero = $this->_valida_numero($numero);

        # Params iniciales
        $params_peticion = array(
            'idioma' => 'es',
            'dispatch' => 'doRastreoInternet',
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
        $presas[] = array('numero_guia', new KeyValue('mero de gu'));
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
        $presas[] = array('peso_volumetrico', new KeyValue('Peso volumétrico kg'));
        $presas[] = array('recibio', new KeyValue('recibi'));
        $presas[] = array('dimensiones', new KeyValue('Dimensiones cm'));
        $columnas = array('fecha', 'lugar_movimiento', 'comentarios');
        $presas[] = array('movimientos', new Tabla(array('ocurrencia' => -1), $columnas, 3));
        $hunter->arrPresas = $presas;
        $hunted = $hunter->hunt();
        if (strpos($hunted['estatus'], 'No hay informaci') !== false) {
            return array('estatus' => $hunted['estatus']);
        } else {
            # Busca coordenadas de origen
            try {
                $ciudad = $hunted['origen'];
                $direccion = $ciudad . ', Mexico';
                $geocoder = new GoogleMapsGeocoder($direccion);
                $response = $geocoder->geocode();
                $latitud = $response['results'][0]['geometry']['location']['lat'];
                $longitud = $response['results'][0]['geometry']['location']['lng'];
                $hunted['origen'] = array(
                    'nombre' => $ciudad,
                    'latitud' => $latitud,
                    'longitud' => $longitud
                );
            } catch (Exception $e) {
                
            }

            # Busca coordenadas de destino
            try {
                $ciudad = $hunted['destino'];
                $cp = $hunted['cp_destino'];
                $direccion = $ciudad . ', Mexico';
                $geocoder = new GoogleMapsGeocoder($direccion);
                $response = $geocoder->geocode();
                $latitud = $response['results'][0]['geometry']['location']['lat'];
                $longitud = $response['results'][0]['geometry']['location']['lng'];
                unset($hunted['cp_destino']);
                $hunted['destino'] = array(
                    'nombre' => $ciudad,
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'cp_destino' => $cp
                );
            } catch (Exception $e) {
                
            }

            # Descomposicion de dimensiones, de string a ancho, alto, largo
            $dimensiones = $hunted['dimensiones'];
            $piezas_dimensiones = explode('x', $dimensiones);
            $alto = $piezas_dimensiones[0];
            $largo = $piezas_dimensiones[1];
            $ancho = $piezas_dimensiones[2];
            $hunted['dimensiones'] = array('alto' => $alto, 'largo' => $largo, 'ancho' => $ancho);

            # Construye historial de movimientos
            $historial = $hunted['movimientos'];
            $movimientos = array();
            foreach ($historial as $evento) {
                $movimiento = array();
                $movimiento['descripcion'] = $texto;
                $movimiento['fecha'] = $evento['fecha'];
                # Comentado por buggiento https://github.com/ivanrodriguez/dom-hunter/issues/4
                # movimiento['comentarios'] = $evento['comentarios'];
                $movimiento['id'] = _asigna_id_movimiento($texto);
                $movimientos[] = $movimiento;
            }
            $hunted['movimientos'] = $movimientos;

            # Firma de recibido
            $presas = array();
            $opciones_navegacion = array('getElementById' => $hunted['numero_guia'] . 'FIR', 'nextSibling' => '', 'find' => 'img');
            $presas[] = array('firma_recibido', new NodoDom(array('navegacion' => $opciones_navegacion), 'src'));
            $hunter->arrPresas = $presas;
            $hunted_firma = $hunter->hunt();
            if (isset($hunted['firma_recibido'])) {
                $hunted['firma_recibido'] = URL_FIRMA . $hunted_firma['firma_recibido'];
            } else {
                $hunted['firma_recibido'] = '';
            }

            # Comprobante de entrega
            $hunted['comprobante_entrega'] = URL_COMPROBANTE . $hunted['numero_guia'];

            return $hunted;
        }
    }

    # Endpoint de /cotizacion?cp_origen=01210&cp_destino=86035

    public function cotizacion($cp_origen = NULL, $cp_destino = NULL, $tipo = 'sobre', $peso = NULL, $alto = NULL, $largo = NULL, $ancho = NULL) {
        if (!$cp_origen) {
            return array('mensaje_error' => 'Falta el parametro "cp_origen"', 'error' => 3);
        }
        if (!$cp_destino) {
            return array('mensaje_error' => 'Falta el parametro "cp_destino"', 'error' => 4);
        }
        if (!preg_match("/^[0-9]{5}$/", $cp_origen) || !preg_match("/^[0-9]{5}$/", $cp_destino)) {
            return array('mensaje_error' => 'No es un codigo postal de origen o destino valido', 'error' => 5);
        }
        $params_peticion = array(
            'CPOrigen' => $cp_origen,
            'CPDestino' => $cp_destino,
            'Tipo' => $tipo,
            'cTipoEnvio' => $tipo
        );

        # Paquetes
        if ($tipo == 'paquete') {
            if (!$peso) {
                return array('mensaje_error' => 'Falta el parametro "peso" para cotizar paquetes', 'error' => 6);
            }
            if (!$alto) {
                return array('mensaje_error' => 'Falta el parametro "alto" para cotizar paquetes', 'error' => 7);
            }
            if (!$largo) {
                return array('mensaje_error' => 'Falta el parametro "largo" para cotizar paquetes', 'error' => 8);
            }
            if (!$ancho) {
                return array('mensaje_error' => 'Falta el parametro "ancho" para cotizar paquetes', 'error' => 9);
            }
            $params_peticion['Peso'] = $peso;
            $params_peticion['Alto'] = $alto;
            $params_peticion['Largo'] = $largo;
            $params_peticion['Ancho'] = $ancho;
        }

        # Busca dom
        $hunter = new DomHunter(URL_COTIZAR, 1);
        $hunter->arrParamsPeticion = $params_peticion;
        $presas = array();
        $columnas = array('producto', 'peso_kg', 'tarifa_guia', 'tarifa_combustible', 'cargos_extra', 'sobrepeso_costo', 'sobrepeso_combustible', 'costo_total');
        $presas[] = array('precios', new Tabla(array('ocurrencia' => -1), $columnas, 10));
        $hunter->arrPresas = $presas;
        $hunted = $hunter->hunt();
        return $hunted;
    }

    private function _valida_numero($numero) {
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

    public function id_movimiento($texto) {
        # En proceso de entrega
        if (preg_match('/\bEN PROCESO DE ENTREGA\b/i', $texto)) {
            return 1;
        }
        # Llegada a CEDI
        if (preg_match('/\bLLEGADA A CENTRO DE DISTRIBUCI\b/i', $texto)) {
            return 2;
        }
        # En ruta foránea  hacia un destino
        if (preg_match('/\bEN RUTA FOR\b/i', $texto)) {
            return 3;
        }
        # Recolección en oficina por ruta local
        if (preg_match('/\bN EN OFICINA\b/i', $texto)) {
            return 4;
        }
        # Recibido en oficina
        if (preg_match('/\bRECIBIDO EN OFICINA\b/i', $texto)) {
            return 5;
        }
        # Movimiento en CEDI
        if (preg_match('/\bMOVIMIENTO EN CENTRO DE DISTRIBUCI\b/i', $texto)) {
            return 6;
        }
        # Aclaracion en proceso
        if (preg_match('/\bN EN PROCESO\b/i', $texto)) {
            return 7;
        }
        # En ruta local
        if (preg_match('/\bEN RUTA LOCAL\b/i', $texto)) {
            return 8;
        }
        # Movimiento local
        if (preg_match('/\bMOVIMIENTO LOCAL\b/i', $texto)) {
            return 9;
        }
        return -1;
    }

}

?>