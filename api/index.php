<?php
// Config general
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

// Includes
include_once 'simple_html_dom.php';
include_once 'prettyJson.php';
include_once 'funciones.php';
include_once 'geocoder.php';

/*
 * En buscarDom.php viene el numero de nodo text que le toca, si cambia el DOM
 * cambia este mapeo
 * 
 * Número de guía (text, 60)
 * Código de rastreo (text, 64)
 * Origen (text, 73)
 * Destino (text, 80)
 * CP Destino (text, 84)
 * Estatus del servicio (text, 97)
 * Servicio (text, 135)
 * Fecha y hora de entrega (text, 139)
 * Fecha programada de entrega (text, 155)
 * Fecha de recolección (text, 167)
 * 
 * Agregados en el fix de 22.02.14
 * 
 * Tipo de envío (text, 149)
 * Recibió (text, 102)
 * Firma de Recibido (img, 4)
 * Dimensiones cm (text, 252)
 * Peso kg (text, 261)
 * Peso volumétrico kg (text, 267)
 */

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);

$data = array(
    "idioma" => 'es',
    "dispatch" => 'doRastreoInternet'
);

if (!isset($_GET['numero'])) {
    $fields = array(
        "error" => 1,
        "mensaje_error" => "No existe el parámetro numero en la peticion GET",
    );
    die(indent(json_encode($fields)));
}
if (valida('guia')) {
    $data['guias'] = $_GET['numero'];
    $data['tipoGuia'] = 'ESTAFETA';
} elseif (valida('rastreo')) {
    $data['guias'] = $_GET['numero'];
    $data['tipoGuia'] = 'REFERENCE';
} else {
    $fields = array(
        "error" => 2,
        "mensaje_error" => "Número de guía o código de rastreo no válidos",
    );
    die(indent(json_encode($fields)));
}

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$output = curl_exec($ch);
curl_close($ch);

$html = new simple_html_dom();
$html->load($output);

// No hay informacion disponible
if (trim(str_replace('&nbsp;', '', $html->find('text', 248)->plaintext)) == "No hay información disponible.") {
    $fields = array(
        "error" => 2,
        "mensaje_error" => "No hay información disponible.",
    );
    die(indent(json_encode($fields)));
}

// Busca coordenadas de origen y destino
$ciudadOrigen = trim(str_replace('&nbsp;', '', $html->find('text', 73)->plaintext));
$coords = geocoder::getLocation($ciudadOrigen . ", Mexico");
if ($coords != false) {
    $latitudOrigen = $coords['lat'];
    $longitudOrigen = $coords['lng'];
} else {
    $latitudOrigen = "";
    $longitudOrigen = "";
}
$ciudadDestino = trim(str_replace('&nbsp;', '', $html->find('text', 80)->plaintext));
$coords = geocoder::getLocation($ciudadDestino . ", Mexico");
if ($coords != false) {
    $latitudDestino = $coords['lat'];
    $longitudDestino = $coords['lng'];
} else {
    $latitudDestino = "";
    $longitudDestino = "";
}

// Descomposicion de dimensiones, de string a ancho, alto, largo
$dimensiones = $html->find('text', 252)->plaintext;
$arrDimensiones = explode('x', $dimensiones);
$ancho = $arrDimensiones[0];
$alto = $arrDimensiones[1];
$largo = $arrDimensiones[2];

// Construye respuesta de guia/rastreo encontrado
try {
    $fields = array(
        "numero_guia" => trim(str_replace('&nbsp;', '', $html->find('text', 60)->plaintext)),
        "codigo_rastreo" => trim(str_replace('&nbsp;', '', $html->find('text', 64)->plaintext)),
        "servicio" => trim(str_replace('&nbsp;', '', utf8_encode($html->find('text', 135)->plaintext))),
        "fecha_programada" => trim(str_replace('&nbsp;', '', $html->find('text', 155)->plaintext)),
        "origen" => array(
            "nombre" => trim($html->find('text', 73)->plaintext),
            "latitud" => $latitudOrigen,
            "longitud" => $longitudOrigen
        ),
        "fecha_recoleccion" => trim(str_replace('&nbsp;', '', $html->find('text', 167)->plaintext)),
        "destino" => array(
            "nombre" => trim(str_replace('&nbsp;', '', $html->find('text', 80)->plaintext)),
            "latitud" => $latitudDestino,
            "longitud" => $longitudDestino,
            "codigo_postal" => trim(str_replace('&nbsp;', '', $html->find('text', 84)->plaintext))
        ),
        "estatus_envio" => trim(str_replace('&nbsp;', '', $html->find('text', 97)->plaintext)),
        "fecha_entrega" => trim(str_replace('&nbsp;', '', $html->find('text', 139)->plaintext)),
        // Agregados en el fix de 22.02.14
        "tipo_envio" => trim(str_replace('&nbsp;', '', $html->find('text', 149)->plaintext)),
        "recibio" => trim(str_replace('&nbsp;', '', $html->find('text', 102)->plaintext)),
        "firma_recibido" => 'http://rastreo3.estafeta.com' . $html->find('img', 4)->src,
        "dimensiones" => array(
            "ancho" => $ancho,
            "alto" => $alto,
            "largo" => $largo
        ),
        "peso" => trim(str_replace('&nbsp;', '', $html->find('text', 261)->plaintext)),
        "peso_volumetrico" => trim(str_replace('&nbsp;', '', $html->find('text', 267)->plaintext))
    );
    $fields = limpiaRespuesta($fields);
    echo indent(json_encode($fields));
} catch (Exception $e) {
    $fields = array(
        "error" => 2,
        "mensaje_error" => $e,
    );
    die(indent(json_encode($fields)));
}