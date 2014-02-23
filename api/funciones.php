<?php

// Valida número de guía y código de rastreo
function valida($tipo) {
    if ($tipo == 'guia') {
        // 22 y alfanumérico
        if (strlen($_GET['numero']) != 22)
            return false;
        if (!ctype_alnum($_GET['numero'])) {
            return false;
        }
        return true;
    }
    if ($tipo == 'rastreo') {
        // 10 y numérico
        if (strlen($_GET['numero']) != 10)
            return false;
        if (!is_numeric($_GET['numero']))
            return false;
        return true;
    }
}

// Valida cada campo de la respuesta y reemplaza si está mal
function limpiaRespuesta($respuesta) {
    // numero de guia ya se validó
    // codigo de rastreo ya se validó
    // Tipo de servicio
    // Fecha programada de entrega, fecha de recoleccion, fecha de entrega
    /*
      if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $respuesta["fecha_programada"]))
      $respuesta["fecha_programada"] = "";
      if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $respuesta["fecha_recoleccion"]))
      $respuesta["fecha_recoleccion"] = "";
      if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $respuesta["fecha_entrega"]))
      $respuesta["fecha_entrega"] = "";
     */
    // Nombre origen, destino
    if ($respuesta["origen"]["latitud"] == "")
        $respuesta["origen"]["nombre"] = "";
    if ($respuesta["destino"]["latitud"] == "")
        $respuesta["destino"]["nombre"] = "";
    // Coordenadas origen y destino ya se validaron
    // codigo postal destino
    if (!preg_match('/[0-9][0-9][0-9][0-9][0-9]/', $respuesta["destino"]["codigo_postal"]))
        $respuesta["destino"]["codigo_postal"] = "";
    // Estatus envio
    return $respuesta;
}
?>
