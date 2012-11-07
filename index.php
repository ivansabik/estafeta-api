<?php
include_once 'simple_html_dom.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);

$data = array(
  "idioma" => 'es',
  "dispatch" => 'doRastreoInternet',
  "tipoGuia" => 'REFERENCE', // estafeta si es rastreo
  "guias" => '1527923911'
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$output = curl_exec($ch);
curl_close($ch);

$html = new simple_html_dom();
$html->load($output);

echo("<p>".$html->find('span', 0)->plaintext."</p>");
echo("<p>".$html->find('span', 1)->plaintext."</p>");
echo("<p>".$html->find('span', 2)->plaintext."</p>");
echo("<p>".$html->find('span', 3)->plaintext."</p>");
echo "<p>".$html->find('span.style4 text',0)->plaintext."</p>";
echo "<p>".$html->find('span.style4 div text',0)->plaintext."</p>";
echo "<p>".$html->find('span.style4 span span text',0)->plaintext.$html->find('span.style4 span span text',1)->plaintext."</p>";
echo "<p>".$html->find('div.style17',0)->plaintext."</p>";
echo "<p>".$html->find('div span.style1 text',2)->plaintext."</p>";
echo "<p>".$html->find('div span.style1 span text',0)->plaintext."</p>";
echo("<p>".$html->find('div span.style1 span span text', 0)->plaintext."</p>");
echo("<p>".$html->find('span', 13)->plaintext."</p>");
//Destino MEXICO D.F.
//CP Destino 01210
//Estatus del envío PENDIENTE EN TRANSITO
//Fecha y hora de entrega 
?>