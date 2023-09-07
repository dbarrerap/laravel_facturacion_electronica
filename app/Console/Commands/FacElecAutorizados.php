<?php

namespace App\Console\Commands;

use App\Models\FacturaElectronica;
use App\Models\TipoErrores;
use DOMDocument;
use Illuminate\Console\Command;
use SoapClient;

class FacElecAutorizados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facelec:autorizados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solicita la confirmacion del estado de documentos electronicos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $recibidos = FacturaElectronica::where('estado', '=', 'R')->whereNull('identificador')->get();
        $bar = $this->output->createProgressBar(count($recibidos));

        foreach($recibidos as $recibido) {
            try {
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';  // PRUEBAS
                // $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/AutorizacionComprobantesOffline?wsdl';  // PRODUCCION
                $webServiceAutorizacion = new SoapClient($url);
                $result = $webServiceAutorizacion->autorizacionComprobante(array('claveAccesoComprobante' => $recibido['clave_acceso']));

                $autorizacionNodo = $result->RespuestaAutorizacionComprobante->autorizaciones;
                if (isset($autorizacionNodo)) {
                    if ($autorizacionNodo->autorizacion->estado == 'AUTORIZADO') {
                        // Obtener el contenido del nodo <autorizacion>
                        $autorizacionNode = $autorizacionNodo->autorizacion;

                        // Obtener los valores de los nodos hijos de <autorizacion>
                        $estado = $autorizacionNode->estado;
                        $numeroAutorizacion = $autorizacionNode->numeroAutorizacion;
                        $fechaAutorizacion = $autorizacionNode->fechaAutorizacion;
                        $ambiente = $autorizacionNode->ambiente;

                        // Obtener el contenido del nodo <comprobante> dentro de CDATA
                        $comprobanteCdata = trim($autorizacionNode->comprobante);

                        // Crear un nuevo documento XML
                        $dom = new DOMDocument('1.0', 'UTF-8');
                        $dom->preserveWhiteSpace = false;
                        $dom->formatOutput = true;

                        // Crear el nodo <autorizacion> en el nuevo documento
                        $autorizacionDomNode = $dom->createElement('autorizacion');

                        // Agregar nodos hijos a <autorizacion>
                        $estadoNode = $dom->createElement('estado', $estado);
                        $autorizacionDomNode->appendChild($estadoNode);

                        $numeroAutorizacionNode = $dom->createElement('numeroAutorizacion', $numeroAutorizacion);
                        $autorizacionDomNode->appendChild($numeroAutorizacionNode);

                        $fechaAutorizacionNode = $dom->createElement('fechaAutorizacion', $fechaAutorizacion);
                        $autorizacionDomNode->appendChild($fechaAutorizacionNode);

                        $ambienteNode = $dom->createElement('ambiente', $ambiente);
                        $autorizacionDomNode->appendChild($ambienteNode);

                        // Crear el nodo <comprobante> dentro de CDATA y agregarlo a <autorizacion>
                        $comprobanteCdataNode = $dom->createCDATASection($comprobanteCdata);
                        $comprobanteNode = $dom->createElement('comprobante');
                        $comprobanteNode->appendChild($comprobanteCdataNode);
                        $autorizacionDomNode->appendChild($comprobanteNode);

                        // Agregar <autorizacion> al documento
                        $dom->appendChild($autorizacionDomNode);

                        // Ruta del archivo de salida
                        $archivoSalida = storage_path('app/comprobantes/autorizados/' . $recibido->clave_acceso . '.xml');

                        // Guardar el contenido en el archivo
                        $dom->save($archivoSalida);

                        // Guarda los datos en la base
                        $fechaAutorizacion = str_replace("T", " ", ($autorizacionNodo->autorizacion->fechaAutorizacion));
                        $separando = explode("-", $fechaAutorizacion);
                        $fechaAutorizacion = $separando[0] . "-" . $separando[1] . "-" . $separando[2];

                        $datosAct = [
                            'estado' => 'A',
                            'observaciones' => 'Se Autorizo el comprobante correctamente',
                            'fecha_autorizacion' => $fechaAutorizacion,
                            'clave_autorizacion' => $autorizacionNodo->autorizacion->numeroAutorizacion,
                            'identificador' => null,
                            'mensaje_error' => null,
                        ];
                        FacturaElectronica::where(['clave_acceso' => $recibido['clave_acceso']])->update($datosAct);

                        $this->call('jreport:generar', ['numero' => $recibido['id'], 'clave' => $recibido['clave_acceso']]);
                    } else {
                        $identificador = $autorizacionNodo->autorizacion->mensajes->mensaje->identificador;

                        $mensajeError = TipoErrores::where([
                            'identificador' => $identificador,
                            'tipo' => 'AUTORIZACION'
                        ])->first();

                        $informacionAdicional = 'Sin informacion adicional';
                        if (isset($autorizacionNodo->autorizacion->mensajes->mensaje->informacionAdicional)) {
                            $informacionAdicional = $autorizacionNodo->autorizacion->mensajes->mensaje->informacionAdicional;
                        }

                        $datosAct = [
                            'identificador' => $identificador,
                            "mensaje_error" => $mensajeError->descripcion . " (" . $informacionAdicional . ")",
                        ];
                        FacturaElectronica::where(['clave_acceso' => $recibido['clave_acceso']])->update($datosAct);
                    }
                } else {
                    $datosAct = [
                        'identificador' => null,
                        'mensaje_error' => 'AutorizacionXML: Respuesta vacia'
                    ];
                    FacturaElectronica::where(['clave_acceso' => $recibido['clave_acceso']])->update($datosAct);
                }
                //
            } catch (\Exception $ex) {
                $datosAct = [
                    'identificador' => null,
                    'mensaje_error' => 'AutorizacionXML: Error al confirmar autorizacion de XML (' . $ex->getMessage() . ')'
                ];
                FacturaElectronica::where(['clave_acceso' => $recibido['clave_acceso']])->update($datosAct);
            } 
            //
            $bar->advance();
        }
        $bar->finish();

        return Command::SUCCESS;
    }
}
