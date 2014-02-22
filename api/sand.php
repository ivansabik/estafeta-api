<?php
include_once 'simple_html_dom.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);

$data = array(
  "idioma" => 'es',
  "dispatch" => 'doRastreoInternet',
  "tipoGuia" => 'REFERENCE',
  "guias" => '3563581975'
  //"guias" => '1872996868'
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$output = curl_exec($ch);
curl_close($ch);

$html = new simple_html_dom();
$html->load($output);

$i = 0;
$searchKey = "text";
foreach($html->find($searchKey) as $txt) {
	echo($i."&nbsp;<p>".$html->find($searchKey, $i)."</p>");
	$i++;
}
?>
