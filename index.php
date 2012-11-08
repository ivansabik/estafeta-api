<?php
// TODO: Historial del envío, Fechas y horas separar, Origen y destino con coordenadas
header('Content-Type: application/json; charset=utf-8');

include_once 'simple_html_dom.php';
include_once 'prettyJson.php';
include_once 'funciones.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, true);

$data = array(
	  "idioma" => 'es',
	  "dispatch" => 'doRastreoInternet'
	  );

if(isset($_GET['numero']) && valida('guia')) {
 	$data['guias'] = $_GET['numero'];
	$data['tipoGuia'] = 'ESTAFETA';
		
}
else if(isset($_GET['numero']) && valida('rastreo')){
	$data['guias'] = $_GET['numero'];
	$data['tipoGuia'] = 'REFERENCE';
}
else {
	responde();
}

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$output = curl_exec($ch);
curl_close($ch);

$html = new simple_html_dom();
$html->load($output);

// Ver si es respuesta "No hay información disponible" para llamar responde()
// Try catch para otras cosas raras

$fields = array(
	"numero_guia" => $html->find('div span text', 1)->plaintext,
	"codigo_rastreo" => $html->find('div span text', 3)->plaintext,
	"servicio" => utf8_encode($html->find('div span text', 8)->plaintext),
	"fecha_programada" => $html->find('div span text', 15)->plaintext,
	"origen" => utf8_encode($html->find('div span text', 20)->plaintext),
	"fecha_hora_recoleccion" => $html->find('div span text', 26)->plaintext,
	"destino" => utf8_encode($html->find('div span text', 31)->plaintext),
	"cp_destino" => $html->find('div span text', 37)->plaintext,
	"estatus_envio" => utf8_encode($html->find('div span text', 42)->plaintext),
	"fecha_hora_entrega" => $html->find('div span text', 48)->plaintext
);

// Si es error retornar fields error => id, mensaje_error => texto
echo indent(json_encode($fields));
?>