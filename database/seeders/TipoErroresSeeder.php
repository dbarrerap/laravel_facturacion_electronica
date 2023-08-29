<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoErroresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fe_errores')->insert([
            ['identificador' => 2, 'descripcion' => 'RUC del emisor se encuentra NO ACTIVO.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 10, 'descripcion' => 'Establecimiento del emisor se encuentra Clausurado.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 26, 'descripcion' => 'Tamaño maximo superado. Tamaño del archivo supera lo establecido.', 'tipo' => 'RECEPCION'],
            ['identificador' => 27, 'descripcion' => 'Clase no permitido. La clase del contribuyente no puede emitir comprobantes electronicos.', 'tipo' => 'RECEPCION'],
            ['identificador' => 28, 'descripcion' => 'Acuerdo de medios electronicos no aceptado.', 'tipo' => 'RECEPCION'],
            ['identificador' => 35, 'descripcion' => 'Documento Invalido. XML no se ajusta al esquema XSD.', 'tipo' => 'RECEPCION'],
            ['identificador' => 36, 'descripcion' => 'Version esquema descontinuada. Cuando la version del esquema no es la correcta.', 'tipo' => 'RECEPCION'],
            ['identificador' => 37, 'descripcion' => 'RUC sin autorizacion de emision. Cuando el RUC del emisor no cuenta con una solicitud de emisión de comprobantes electrónicos.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 39, 'descripcion' => 'Firma invalida. Firma electrónica del emisor no es válida.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 40, 'descripcion' => 'Error en el Certificado. No se encontró el certificado o no se puede convertir en certificad X509.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 43, 'descripcion' => 'Clave acceso registrada. Cuando la clave de acceso ya se encuentra registrada en la base de datos.', 'tipo' => 'RECEPCION'],
            ['identificador' => 45, 'descripcion' => 'Secuencial registrado. Secuencial del comprobante ya se encuentra registrado en la base de datos', 'tipo' => 'RECEPCION'],
            ['identificador' => 46, 'descripcion' => 'RUC no existe. Cuando el RUC emisor no existe en el Registro Único de Contribuyentes.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 47, 'descripcion' => 'Tipo de Comprobante no existe. Cuando envían en el tipo de comprobante uno que no exista en el catálogo de nuestros tipos de comprobantes.', 'tipo' => 'RECEPCION'],
            ['identificador' => 48, 'descripcion' => 'Esquema XSD no existe. Cuando el esquema para el tipo de comprobante enviado no existe.', 'tipo' => 'RECEPCION'],
            ['identificador' => 49, 'descripcion' => 'Argumentos que envían al WS nulos. Cuando se consume el WS con argumentos nulos.', 'tipo' => 'RECEPCION'],
            ['identificador' => 50, 'descripcion' => 'Error interno general. Cuando ocurre un error inesperado en el servidor.', 'tipo' => 'RECEPCION'],
            ['identificador' => 52, 'descripcion' => 'Error en diferencias. Cuando existe error en los cálculos del comprobante.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 56, 'descripcion' => 'Establecimiento Cerrado. Cuando el establecimiento desde el cual se genera el comprobante se encuentra cerrado.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 57, 'descripcion' => 'Autorizacion suspendida. Cuando la autorización para emisión de comprobantes electrónicos para el emisor se encuentra suspendida por procesos de control de la Administración Tributaria.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 58, 'descripcion' => 'Error en la estructura de clave acceso. Cuando la clave de acceso tiene componentes diferentes a los del comprobante.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 63, 'descripcion' => 'RUC clausurado. Cuando el RUC del emisor se encuentra clausurado por procesos de control de la Administración Tributaria.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 65, 'descripcion' => 'Fecha de emisión extemporánea. Cuando el comprobante emitido no fue enviado de acuerdo con el tiempo del tipo de emisión en el cual fue realizado.', 'tipo' => 'RECEPCION'],
            ['identificador' => 67, 'descripcion' => 'Fecha Invalida. Cuando existe errores en el formato de la fecha.', 'tipo' => 'RECEPCION'],
            ['identificador' => 70, 'descripcion' => 'Clave de acceso en procesamiento. Cuando se desea enviar un comprobante que ha sido enviado anteriormente y el mismo no ha terminado su procesamiento.', 'tipo' => 'RECEPCION'],
            ['identificador' => 80, 'descripcion' => 'Error en la estructura de clave acceso. Cuando se ejecuta la consulta de autorización por clave de acceso y el valor de este parámetro supera los 49 dígitos, tiene caracteres alfanuméricos o cuando el tag (claveAccesoComprobante) está vacío.', 'tipo' => 'AUTORIZACION'],
            ['identificador' => 82, 'descripcion' => 'Error en la fecha de inicio de transporte. Cuando la fecha de inicio de transporte es menor a la fecha de emisión de la guía de remisión.', 'tipo' => 'RECEPCION'],
            ['identificador' => 92, 'descripcion' => 'Error al validar monto de devolución del IVA. Cuando el valor registrado en el campo de devolución del IVA, en facturas y notas de débito, no corresponde al que fue autorizado por el servicio web DIG.', 'tipo' => 'RECEPCION'],
        ]);
    }
}
