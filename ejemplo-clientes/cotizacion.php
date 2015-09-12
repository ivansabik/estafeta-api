<?php

$cpOrigen = $_POST['cp_origen'];
$cpDestino = $_POST['cp_destino'];
$tipo = $_POST['tipo'];
$peso = $_POST['peso'];
$alto = $_POST['alto'];
$ancho = $_POST['ancho'];
$largo = $_POST['largo'];

define('URL_API', 'http://localhost:8000/index.php/estafeta/cotizacion');

$parametrosApiCall = array(
    'cp_origen' => $cpOrigen,
    'cp_destino' => $cpDestino,
    'tipo' => $tipo,
    'peso' => $peso,
    'alto' => $alto,
    'ancho' => $ancho,
    'largo' => $largo
);

$url = URL_API . '?' . http_build_query($parametrosApiCall);
$respuestaJson = file_get_contents($url);
$respuesta = json_decode($respuestaJson);

include('cotizacion.htm');
print '<p></p><div>';
print '<pre>';
var_dump($respuesta);
print '</pre>';
print '</div></body></html>';
?>
