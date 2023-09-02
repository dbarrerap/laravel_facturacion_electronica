# Gestion de Comprobantes Electronicos

Esta aplicacion genera comprobantes electronicos cumpliendo con los requisitos legales
y reglamentarios exigibles para todos los comprobantes de venta, garantizando la
autenticidad de su origen y la integridad de su contenido y que se emite a través de
una nueva modalidad electrónica autorizada por el Servicio de Rentas Internas.

Esta aplicacion es un complemento para (Stock Manager Advance)[https://tecdiary.net/products/stock-manager-advance-with-all-modules], una aplicacion para manejo de inventario y facturacion.

## Requerimientos

* PHP 8.0+ (ext: php-soap)
* composer 2+

## Instalacion

1. Instalar las dependencias del proyecto `composer install`
1. Generar el archivo env `cp .env.example .env`
1. Generar la clave del programa `php artisan key:generate`
1. Migrar las tablas para la generacion de Documentos Electronicos `php artisan migrate`
1. Cargar los datos iniciales para manejo de errores `php artisan db:seed --class TipoErroresSeeder`
1. [Descargar](https://github.com/jordiicabrera/FirmaSriJava) el firmador. Copiar el JAR y la carpeta lib (ubicados en la carpeta dist) en la carpeta `storage/app/firmador`. (Requiere JRE 11)
1. Ubicar el certificado p12 junto al JAR, con el nombre `certificado.p12`.
1. Escribir la clave del certificado en el archivo `.env` en la variable `CLAVE_CERTIFICADO`
1. Agregar la tarea cron `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` para la ejecucion de comandos.

## Comprobantes Electronicos
Los comprobantes electronicos pueden ser encontrados en `storage/app/comprobantes`.
