<?php
function valida($tipo) {
	if($tipo == 'guia') {
		// 22 y alfanumérico
		if(strlen($_GET['numero']) != 22)
			return false;
		if(!ctype_alnum($_GET['numero']))
			return false;
		return true;
	}
	if($tipo == 'rastreo') {
		// 10 y numérico
		if(strlen($_GET['numero']) != 10)
			return false;
		if(!is_numeric($_GET['numero']))
			return false;
		return true;		
	}
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