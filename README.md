# Estafeta API
----------------

### Actualización 22/02/2014
Ya funciona otra vez la API! Estafeta cambió un poco el diseño gráfico del sitio y con esto cambió el DOM. Era tan sencillo solucionarlo, pero había que hacerlo manualmente hasta que no funcione DOM Hunter (https://github.com/ivanrodriguez/dom-hunter).

Cambios importantes:

 - ELIMINADO hora_recoleccion ya no existe y en cambio fecha_recoleccion contiene la fecha y hora
 - ELIMINADO hora_entrega, que está implícito en fecha_entrega
 - AGREGADO Tipo de envío
 - AGREGADO Recibió
 - AGREGADO Firma de Recibido
 - AGREGADO Dimensiones cm
 - AGREGADO Peso kg
 - AGREGADO Peso volumétrico kg

Sin relación alguna con Estafeta, es una API desarrollada con PHP que proporciona información relacionada con un envió en formato JSON. Para hacer esto hace una petición POST a la pagina de rastreo de Estafeta y parsea la pagina resultante para construir la información del paquete.

No es estable para desarrollos productivos ya que:

 - Depende de que Estafeta no cambie el DOM de la pagina que contiene la info de los envíos ya que la API parsea en duro de acuerdo a la posición (Ej. el 7 elemento de texto del DOM de la respuesta)
 - Depende de que Estafeta no implemente captcha para solicitar información del envíos
 - Depende de que Estafeta no implemente autentificación, cambie URL o bloquee peticiones externas
 - Depende que no se exceda el limite de búsquedas de la API de geolocalización de Google Maps

Actualmente proporciona la siguiente info (sólo para envíos nacionales)

 - Número de guía Código de rastreo
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

### TODO

 - Generar historia del envío
 - Cache

### Uso

    http://localhost/EstafetaAPI/api?numero=GUIA_O_CODIGO_DE_RASTREO

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
    
Se incluye un ejemplo de aplicación web que consume los servicios de la API para mostrar en un mapa el origen y destino del envío con otra información. Esta aplicación también puede recibir un número de guía o código de rastreo en la URL, por ejemplo:

    http://localhost/EstafetaAPI/client/?numero=1872996868

### Documentacion de respuestas

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