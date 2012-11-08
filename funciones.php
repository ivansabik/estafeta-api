<?php
function valida($tipo) {
	if($tipo == 'guia') // 22 y alfanumérico
		return false;
	if($tipo == 'rastreo') // 10 y numérico
		return true;
}

function responde() {
	$fields = array(
		"error" => 1,
		"mensaje_error" => "NUMERO DE GUÍA O CÓDIGO DE RASTREO NO VÁLIDOS",
	);
	
	$fields = array(
		"error" => 2,
		"mensaje_error" => "NO HAY INFORMACIÓN DISPONIBLE",
	);
	
	echo indent(json_encode($fields));
	exit();
}
?>