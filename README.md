# Gestion de Comprobantes Electronicos

Esta aplicacion genera comprobantes electronicos cumpliendo con los requisitos legales
y reglamentarios exigibles para todos los comprobantes de venta, garantizando la
autenticidad de su origen y la integridad de su contenido y que se emite a través de
una nueva modalidad electrónica autorizada por el Servicio de Rentas Internas.

Esta aplicacion es un complemento para [Stock Manager Advance](https://tecdiary.net/products/stock-manager-advance-with-all-modules), una aplicacion para manejo de inventario y facturacion.

## Requerimientos

* PHP 8.0+ (ext: php-soap)
* composer 2+
* Java JRE 8 (Para Firmado de XML y Generacion de PDF RIDE)

## Instalacion

1. Instalar las dependencias del proyecto `composer install`
1. Generar el archivo env `cp .env.example .env`
1. Generar la clave del programa `php artisan key:generate`
1. Ingresar las credenciales de la base de datos en las variables `DB_*`
1. Migrar las tablas para la generacion de Documentos Electronicos `php artisan migrate`
1. Cargar los datos iniciales para manejo de errores `php artisan db:seed --class TipoErroresSeeder`
1. Configurar los datos de la Empresa `php artisan facelec:config` (Esto se realiza una sola vez)
    * **ATENCION:** El logo de la empresa debe ser convertido a base64 y almacenado en el campo `logo_empresa` de manera manual.
1. [Descargar](https://github.com/jordiicabrera/FirmaSriJava) el firmador. Copiar el JAR y la carpeta lib (ubicados en la carpeta dist) en la carpeta `storage/app/firmador`.
1. Ubicar el certificado p12 junto al JAR del punto anterior.
1. Configurar el nombre del certificado y su clave respectiva en las variables `NOMBRE_CERTIFICADO` y `CLAVE_CERTIFICADO`.
1. Agregar la tarea cron `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` para la ejecucion automatica de comandos.

## Comprobantes Electronicos
Los comprobantes electronicos pueden ser encontrados en `storage/app/comprobantes`.
