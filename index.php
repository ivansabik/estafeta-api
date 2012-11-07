<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);

$data = array(
  "method" => '',
  "forward" =>  '',
  "idioma" => 'es',
   "pickUpId" => '',
  "dispatch" => 'doRastreoInternet',
  "tipoGuia" => 'REFERENCE',
  "guias" => '1527923911',
  "image.x" => '68',
  "image.y"=> '9'
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$output = curl_exec($ch);
curl_close($ch);
print_r($output);


?>