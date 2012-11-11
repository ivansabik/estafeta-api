<?php
// TODO: Historial del envio, trazar poligono en mapa e historial
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');

include_once 'simple_html_dom.php';
include_once 'prettyJson.php';
include_once 'funciones.php';
include_once 'geocoder.php';

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
}
elseif (valida('rastreo')) {
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
if ($html->find('div text', 10)->plaintext == "No hay informaci&oacute;n disponible.") {
    $fields = array(
        "error" => 2,
        "mensaje_error" => "No hay información disponible",
    );
    die(indent(json_encode($fields)));
}

// Busca coordenadas de origen y destino
$coords = geocoder::getLocation(utf8_encode($html->find('div span text', 20)->plaintext) . ", Mexico");
if($coords != false) {
	$latitudOrigen = $coords['lat'];
	$longitudOrigen = $coords['lng'];
} else {
	$latitudOrigen = "";
	$longitudOrigen = "";
}
$coords = geocoder::getLocation(utf8_encode($html->find('div span text', 31)->plaintext) . ", Mexico");
if($coords != false) {
	$latitudDestino = $coords['lat'];
	$longitudDestino = $coords['lng'];
} else {
	$latitudDestino = "";
	$longitudDestino = "";
}

// Construye respuesta de guia/rastreo encontrado
try {
    $fields = array(
        "numero_guia" => $html->find('div span text', 1)->plaintext,
        "codigo_rastreo" => $html->find('div span text', 3)->plaintext,
        "servicio" => utf8_encode($html->find('div span text', 8)->plaintext),
        "fecha_programada" => $html->find('div span text', 15)->plaintext,
        "origen" => array(
            "nombre" => utf8_encode($html->find('div span text', 20)->plaintext),
            "latitud" => $latitudOrigen,
            "longitud" => $longitudOrigen
        ),
        "fecha_recoleccion" => substr($html->find('div span text', 26)->plaintext, 0, 10),
        "hora_recoleccion" => substr($html->find('div span text', 26)->plaintext, 11),
        "destino" => array(
            "nombre" => utf8_encode($html->find('div span text', 31)->plaintext),
            "latitud" => $latitudDestino,
            "longitud" => $longitudDestino,
            "codigo_postal" => $html->find('div span text', 37)->plaintext
        ),
        "estatus_envio" => utf8_encode($html->find('div span text', 42)->plaintext),
        "fecha_entrega" => substr($html->find('div span text', 48)->plaintext, 0, 10),
        "hora_entrega" => substr($html->find('div span text', 48)->plaintext, 11)
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
?>