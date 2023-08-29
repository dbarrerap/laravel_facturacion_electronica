# Gestion de Comprobantes Electronicos

Esta aplicacion genera comprobantes electronicos cumpliendo con los requisitos legales
y reglamentarios exigibles para todos los comprobantes de venta, garantizando la
autenticidad de su origen y la integridad de su contenido y que se emite a través de
una nueva modalidad electrónica autorizada por el Servicio de Rentas Internas.

## Requerimientos
* PHP 8.0+
* composer 2+

## Instalacion

1. `composer install`
1. `php artisan migrate`, crea 2 tablas, `fe_facturas` y `fe_errores`.
1. `php artisan db:seed --class TipoErroresSeeder`, pobla la tabla `fe_errores` con los posibles errores descritos por el SRI.
1. Agregar a crontab `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` para la ejecucion de comandos.
1. [Descargar](https://github.com/jordiicabrera/FirmaSriJava) el firmador y ubicarlo en la carpeta `storage/app/firmador`.
1. Ubicar el certificado p12 junto al firmador.
1. Escribir la clave del certificado en el archivo `.env` en la variable `CLAVE_CERTIFICADO`

## Comprobantes Electronicos
Los comprobantes electronicos pueden ser encontrados en `storage/app/comprobantes`.
