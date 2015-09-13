<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title></title>
</head>
<body>

<div class="TTWForm-container">
     <form action="/cotizacion.php" method="post" novalidate="">
          <div>
               <label for="field3">
                    Tipo de envio
               </label>
               <select name="tipo"  required="required">
                    <option id="sobre" value="sobre">
                         Sobre
                    </option>
                    <option id="paquete" value="paquete">
                         Paquete
                    </option>
               </select>
          </div>
          <div>
               <label for="field4">
                    CP origen
               </label>
               <input type="text" name="cp_origen"  required="required">
          </div>
          <div>
               <label for="field5">
                    CP destino
               </label>
               <input type="text" name="cp_destino"  required="required">
          </div>
          <div>
               <label for="field6">
                    Peso
               </label>
               <input type="text" name="peso"  required="required">
          </div>
          <div>
               <label for="field7">
                    Alto
               </label>
               <input type="text" name="alto"  required="required">
          </div>
          <div>
               <label for="field8">
                    Ancho
               </label>
               <input type="text" name="ancho"  required="required">
          </div>
          <div>
               <label for="field9">
                    Largo
               </label>
               <input type="text" name="largo"  required="required">
          </div>
          <div>
               <input type="submit" value="Submit">
          </div>
     </form>
</div>
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

print '<p></p><div>';
print '<pre>';
var_dump($respuesta);
?>
