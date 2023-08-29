<?php

namespace App\Console\Commands;

use App\Models\FacturaElectronica;
use App\Models\TipoErrores;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SoapClient;

class FacElecEnviar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facelec:enviar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envio de comprobantes electronicos al Servicio Web del SRI via SOAP';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $firmados = FacturaElectronica::where('estado', '=', 'F')->get();
        $bar = $this->output->createProgressBar(count($firmados));

        foreach($firmados as $firmado) {
            try {
                $archivo = Storage::get('comprobantes/firmados/' . $firmado['clave_acceso'] . '.xml');
                // $archivo = base64_encode($archivo);
                
                $xml = array('xml' => $archivo);
                $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl';  // PRUEBAS
                // $url = 'https://cel.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantesOffline?wsdl'; // PRODUCCION
                $webServiceRecepcion = new SoapClient($url);
                $result = $webServiceRecepcion->validarComprobante($xml);

                if (($result->RespuestaRecepcionComprobante->estado) == "RECIBIDA") {
                    $datosAct = [
                        'estado' => 'R',
                        'observaciones' => 'El comprobante electrónico se envió correctamente a recepcion del SRI',
                        'mensaje_error' => null,
                    ];
                    FacturaElectronica::where(['clave_acceso' => $firmado['clave_acceso']])->update($datosAct);
                } else {
                    $identificador = $result->RespuestaRecepcionComprobante->comprobantes->comprobante->mensajes->mensaje->identificador;

                    $mensajeError = TipoErrores::where([
                        'identificador' => $identificador,
                        'tipo' => 'RECEPCION'
                    ])->first();
                    
                    $datosAct = [
                        'identificador' => $identificador,
                        'mensaje_error' => $identificador . " => " . $mensajeError->descripcion,
                    ];
                    FacturaElectronica::where(['clave_acceso' => $firmado['clave_acceso']])->update($datosAct);
                }
            } catch (\Exception $ex) {
                $datosAct = [
                    'mensaje_error' => 'EnviarXML: Error al enviar XML (' . $ex->getMessage() . ')',
                ];
                FacturaElectronica::where('clave_acceso', '=', $firmado['clave_acceso'])->update($datosAct);
            } 
            $bar->advance();
        }
        $bar->finish();

        return Command::SUCCESS;
    }
}
