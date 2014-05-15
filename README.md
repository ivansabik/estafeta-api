# Estafeta API
----------------

API que proporciona información de envios (rastreo y cotizacion) para Estafeta. No es oficial ni tiene relacion con la empresa del mismo nombre.

Actualmente proporciona la siguiente info (sólo para envíos nacionales)

 - Número de guía
 - Código de rastreo
 - Tipo de servicio
 - Fecha programada de entrega
 - Lugar de origen (nombre y coordenadas usando la API de geolocalización Google Maps)
 - Fecha de recolección
 - Hora de recolección
 - Lugar de destino (nombre, código postal y coordenadas usando la API de geolocalización Google Maps)
 - Estatus del envío
 - Fecha de entrega
 - Hora de entrega

### Uso

    rastreo?numero=GUIA_O_CODIGO_DE_RASTREO
    Ej: http://localhost/estafeta-api/index.php/estafeta/rastreo?numero=3039999999061710015581

### Ejemplo de error

    ﻿{
      "error":2,
      "mensaje_error":"No hay informaci\u00f3n disponible"
    }

### Ejemplo de respuesta exitosa

    {
      "numero_guia":"3208544064715720055515",
      "codigo_rastreo":"3563581975",
      "servicio":null,
      "fecha_programada":"17\/02\/2014",
      "origen":{
        "nombre":"Tijuana",
        "latitud":32.5149469,
        "longitud":-117.0382471
      },
      "fecha_recoleccion":"07\/02\/2014 04:43 PM",
      "destino":{
        "nombre":"MEXICO D.F.",
        "latitud":19.4326077,
        "longitud":-99.133208,
        "codigo_postal":"01210"
      },
      "estatus_envio":"Entregado",
      "fecha_entrega":"12\/02\/2014 01:01 PM",
      "tipo_envio":"PAQUETE",
      "recibio":"SDR:JOSE BOLA?OS",
      "firma_recibido":"http:\/\/rastreo3.estafeta.com\/RastreoWebInternet\/firmaServlet?guia=3208544064715720055515&idioma=es",
      "dimensiones":{
        "ancho":"47",
        "alto":"34",
        "largo":"15"
      },
      "peso":"11.8",
      "peso_volumetrico":"4.7"
    }

### Respuestas

| Parametro         | Tipo                                     | Descripcion      |
| ----------------  | ---------------------------------------- | ---------------- | 
| numero_guia       |                                          |                  |
| codigo_rastreo    |                                          |                  |
| servicio          |                                          |                  |
| fecha_programada  |                                          |                  |
| origen            | nombre, latitud, longitud                |                  |
| fecha_recoleccion |                                          |                  |
| destino           | nombre, latitud, longitud, codigo_postal |                  |
| estatus_envio     |                                          |                  |
| fecha_entrega     |                                          |                  |
| tipo_envio        |                                          |                  |
| recibio           |                                          |                  |
| firma_recibido    |                                          |                  |
| dimensiones       | ancho, alto, largo                       |                  |
| peso              |                                          |                  |
| peso_volumetrico  |                                          |                  |