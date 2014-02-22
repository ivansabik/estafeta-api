Estafeta API
----------------

### Actualización 22.02.2014
Ya funciona otra vez la API! Estafeta cambió un poco el diseño gráfico del sitio y con esto cambió el DOM. Era tan sencillo solucionarlo, pero había que hacerlo manualmente hasta que no funcione DOM Hunter (https://github.com/ivanrodriguez/dom-hunter).
Un cambio muy importante también es que hora_recoleccion ya no existe y en cambio fecha_recoleccion contiene la fecha y hora.

Sin relación alguna con Estafeta, es una API desarrollada con PHP que proporciona información relacionada con un envió en formato JSON. Para hacer esto hace una petición POST a la pagina de rastreo de Estafeta y parsea la pagina resultante para construir la información del paquete.

No es estable para desarrollos productivos ya que:

 - Depende de que Estafeta no cambie el DOM de la pagina que contiene la info de los envíos ya que la API parsea en duro de acuerdo a la posición (Ej. el 7 elemento de texto del DOM de la respuesta)
 - Depende de que Estafeta no implemente captcha para solicitar información del envíos
 - Depende de que Estafeta no implemente autentificación, cambie URL o bloquee peticiones externas
 - Depende que no se exceda el limite de búsquedas de la API de geolocalización de Google Maps

Actualmente proporciona la siguiente info

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

TODO:

 - Generar historia del envió
 - Cache

Uso:

    http://localhost/EstafetaAPI/api?numero=GUIA_O_CODIGO_DE_RASTREO

Ejemplo de error

    ﻿{
      "error":2,
      "mensaje_error":"No hay informaci\u00f3n disponible"
    }

Ejemplo de respuesta exitosa

    ﻿{
      "numero_guia":"0019999999703610019220",
      "codigo_rastreo":"1872996868",
      "servicio":"Entrega garantizada al siguiente d\u00eda h\u00e1bil (lunes a viernes)",
      "fecha_programada":"29\/06\/2012",
      "origen":{
        "nombre":"Cd. Ju\u00e1rez",
        "latitud":31.7311292,
        "longitud":-106.4625624
      },
      "fecha_recoleccion":"28\/06\/2012",
      "hora_recoleccion":"01:12 PM",
      "destino":{
        "nombre":"MEXICO D.F.",
        "latitud":19.4326077,
        "longitud":-99.133208,
        "codigo_postal":"01210"
      },
      "estatus_envio":"Entregado",
      "fecha_entrega":"03\/07\/2012",
      "hora_entrega":"05:01 PM"
    }
    
Se incluye un ejemplo de aplicación web que consume los servicios de la API para mostrar en un mapa el origen y destino del envío con otra información. Esta aplicación también puede recibir un número de guía o código de rastreo en la URL, por ejemplo:

    http://localhost/EstafetaAPI/client/?numero=1872996868
