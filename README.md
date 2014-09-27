Estafeta API
================

[![Build Status](https://travis-ci.org/mexicapis/estafeta-api.svg)](https://travis-ci.org/mexicapis/estafeta-api)

API que proporciona información de envios (rastreo y cotizacion) para Estafeta. No es oficial ni tiene relacion con la empresa del mismo nombre.

Actualmente proporciona la siguiente info (sólo para envíos nacionales):

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
 - Firma y comprobante de recibido
 - Historial de movimientos

Para cotización muestra la info de paquetes y sobres de los siguientes productos:

 - 11:30
 - Día siguiente
 - Dos días
 - Terrestre
 
### Deploy local

```$ git clone https://github.com/mexicapis/estafeta-api```

```$ cd estafeta-api```

Las dependencias se descargan con Composer (Restler, Dom Hunter y  Google Maps Geocoder)

```$ composer install```

Si tienes PHP >= 5.4 con el built-in server:

```$ php -S localhost:8000```

### Uso

| Endpoint           | Ejemplo                                                                                                         |
| ------------------ | --------------------------------------------------------------------------------------------------------------- | 
| rastreo            | http://localhost:8000/index.php/estafeta/rastreo?numero=3039999999061710015581                                  |
| cotizacion sobre   | http://localhost:8000/cotizacion?cp_origen=01210&cp_destino=86035                                               |
| cotizacion paquete | http://localhost:8000/cotizacion?cp_origen=01210&cp_destino=86035&tipo=paquete&peso=1&alto=10&ancho=20&largo=20 | 

### Ejemplo de error

    ﻿{
      "error":2,
      "mensaje_error":"No hay información disponible"
    }

### Ejemplo de respuesta exitosa de rastreo

    {
        "numero_guia": "6659999999061710015592",
        "codigo_rastreo": "1749215347",
        "origen": {
            "nombre": "Chetumal",
            "latitud": 18.5001889,
            "longitud": -88.296146
        },
        "destino": {
            "nombre": "Cuernavaca",
            "latitud": 18.9186111,
            "longitud": -99.2341667,
            "cp_destino": "62230"
        },
        "servicio": "Entrega garantizada al tercer día hábil",
        "estatus": "Entregado",
        "fecha_programada": "20/02/2014",
        "fecha_entrega": "24/02/2014 04:41 PM",
        "peso": "22.6",
        "peso_volumetrico": "64.8",
        "recibio": "SDR:JERONIMO DE LA VEGA",
        "dimensiones": {
            "alto": "120",
            "largo": "52",
            "ancho": "52"
        },
        "movimientos": [
            {
                "descripcion": "Cuernavaca En ruta local sin éxito en la entrega",
                "fecha": "24/02/2014 10:28 AM",
                "id": 8
            },
            {
                "descripcion": "Cuernavaca En proceso de entrega CVA Cuernavaca",
                "fecha": "24/02/2014 09:52 AM",
                "id": 1
            },
            {
                "descripcion": "Cuernavaca En proceso de entrega CVA Cuernavaca",
                "fecha": "24/02/2014 09:24 AM",
                "id": 1
            },
            {
                "descripcion": "Cuernavaca Llegada a centro de distribución CVA Cuernavaca",
                "fecha": "22/02/2014 07:23 AM",
                "id": 2
            },
            {
                "descripcion": "MEXICO D.F. En ruta foránea  hacia CVA-Cuernavaca",
                "fecha": "21/02/2014 08:41 PM",
                "id": 3
            },
            {
                "descripcion": "MEXICO D.F. Movimiento en centro de distribución",
                "fecha": "21/02/2014 05:28 PM",
                "id": 6
            },
            {
                "descripcion": "MEXICO D.F. Llegada a centro de distribución",
                "fecha": "21/02/2014 05:24 PM",
                "id": 2
            },
            {
                "descripcion": "Centro de Int. TIN En ruta foránea  hacia CVA-Cuernavaca",
                "fecha": "20/02/2014 11:46 PM",
                "id": 3
            },
            {
                "descripcion": "Mérida En ruta foránea  hacia CVA-Cuernavaca",
                "fecha": "18/02/2014 06:44 AM",
                "id": 3
            },
            {
                "descripcion": "Mérida Llegada a centro de distribución MID Mérida",
                "fecha": "18/02/2014 06:38 AM",
                "id": 2
            },
            {
                "descripcion": "Chetumal En ruta foránea  hacia CVA-Cuernavaca",
                "fecha": "17/02/2014 06:25 PM",
                "id": 3
            },
            {
                "descripcion": "Recolección en oficina por ruta local",
                "fecha": "17/02/2014 06:08 PM",
                "id": 4
            },
            {
                "descripcion": "Envio recibido en oficina Av Cinco de Mayo 25  Centro Chetumal",
                "fecha": "17/02/2014 01:37 PM",
                "id": 5
            }
        ],
        "firma_recibido": "http://rastreo3.estafeta.com/RastreoWebInternet/firmaServlet?guia=6659999999061710015592&idioma=es",
        "comprobante_entrega": "http://rastreo3.estafeta.com/RastreoWebInternet/consultaEnvio.do?dispatch=doComprobanteEntrega&guiaEst=6659999999061710015592"
    }

### Ejemplo de respuesta exitosa de cotización

    {
        "costos": [
            {
                "producto": "11:30",
                "peso_kg": "0.00",
                "tarifa_guia": "192.72",
                "tarifa_combustible": "9.44",
                "cargos_extra": "0.00",
                "sobrepeso_costo": "0.00",
                "sobrepeso_combustible": "0.00",
                "costo_total": "202.16"
            },
            {
                "producto": "Dia Sig.",
                "peso_kg": "0.00",
                "tarifa_guia": "159.15",
                "tarifa_combustible": "7.80",
                "cargos_extra": "0.00",
                "sobrepeso_costo": "0.00",
                "sobrepeso_combustible": "0.00",
                "costo_total": "166.95"
            },
            {
                "producto": "2 Dias",
                "peso_kg": "0.00",
                "tarifa_guia": "129.93",
                "tarifa_combustible": "6.37",
                "cargos_extra": "0.00",
                "sobrepeso_costo": "0.00",
                "sobrepeso_combustible": "0.00",
                "costo_total": "136.30"
            },
            {
                "producto": "Terrestre",
                "peso_kg": "0.00",
                "tarifa_guia": "158.51",
                "tarifa_combustible": "7.77",
                "cargos_extra": "0.00",
                "sobrepeso_costo": "0.00",
                "sobrepeso_combustible": "0.00",
                "costo_total": "166.28"
            }
        ]
    }

### Respuestas rastreo

| Parametro         | Tipo                                     | Descripcion      |
| ----------------  | ---------------------------------------- | ---------------- | 
| numero_guia       |                                          |                  |
| codigo_rastreo    |                                          |                  |
| servicio          |                                          |                  |
| fecha_programada  |                                          |                  |
| origen            | nombre, latitud, longitud                |                  |
| fecha_recoleccion |                                          |                  |
| destino           | nombre, latitud, longitud, codigo_postal |                  |
| estatus           |                                          |                  |
| fecha_entrega     |                                          |                  |
| tipo_envio        |                                          |                  |
| recibio           |                                          |                  |
| firma_recibido    |                                          |                  |
| dimensiones       | ancho, alto, largo                       |                  |
| peso              |                                          |                  |
| peso_volumetrico  |                                          |                  |
| movimientos       |                                          |                  |

### Respuestas cotizacion

| Parametro         | Tipo                                     | Descripcion      |
| ----------------  | ---------------------------------------- | ---------------- | 
| costos            |                                          |                  |

### Todos

  - Versión línea de comandos
  - Buscar zonas
