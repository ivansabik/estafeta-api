<?php

$numero = $_POST['numero'];

define('URL_API', 'http://localhost:8000/index.php/estafeta/rastreo');

$parametrosApiCall = array(
    'numero' => $numero
);

$url = URL_API . '?' . http_build_query($parametrosApiCall);
$respuestaJson = file_get_contents($url);
$respuesta = json_decode($respuestaJson);

include('rastreo.htm');
print '<p></p><div>';
print '<pre>';
var_dump($respuesta);
print '</pre>';
print '</div></body></html>';
?>
