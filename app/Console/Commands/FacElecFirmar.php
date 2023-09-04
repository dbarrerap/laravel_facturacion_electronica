<?php

namespace App\Console\Commands;

use App\Models\FacturaElectronica;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacElecFirmar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facelec:firmar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Firmado de los XML generados';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $documentos = FacturaElectronica::where('estado', '=', 'G')->get();
        $bar = $this->output->createProgressBar(count($documentos));
        foreach($documentos as $documento) {
            try {
                $firmador = storage_path('app/firmador/FirmaElectronica.jar');
                if (!isset($firmador) || empty($firmador)) {
                    $mensaje = 'No se ha encontrado el firmador.';
                    Log::warning($mensaje);
                    throw new \Exception($mensaje);
                }

                $nombre_certificado = env('NOMBRE_CERTIFICADO');
                if (!isset($nombre_certificado)) {
                    $mensaje = 'No se ha configurado el nombre del certificado.';
                    Log::warning($mensaje);
                    throw new \Exception($mensaje);
                }

                $cerificado = storage_path('app/firmador/' . $nombre_certificado);
                $clave_certificado = env('CLAVE_CERTIFICADO');
                if (!isset($clave_certificado)) {
                    $mensaje = 'No se ha configurado la clave del certificado.';
                    Log::warning($mensaje);
                    throw new \Exception($mensaje);
                }

                $input_file = storage_path('app/comprobantes/generados/' . $documento['clave_acceso'] . ".xml ");
                $output_path = storage_path('app/comprobantes/firmados/');
                $output_file = $documento['clave_acceso'] . ".xml";

                $command = 'java -jar ' . $firmador . ' "' . $cerificado . '" ' . $clave_certificado . ' ' . $input_file . ' ' . $output_path . ' '. $output_file;
                exec($command, $o);

                foreach($o as $line) {
                    Log::info($line);
                }

                if (Str::startsWith($o[count($o) - 1], 'Error')) {
                    throw new \Exception($o[count($o) - 1]);
                }
                
                $datosAct = [
                    'observaciones' => 'XML firmado correctamente',
                    'estado' => 'F',
                    'mensaje_error' => null,
                ];
                FacturaElectronica::where(['clave_acceso' => $documento['clave_acceso']])->update($datosAct);
            } catch (\Exception $ex) {
                $datosAct = [
                    'mensaje_error' => 'FirmarXML: Error al firmar XML (' . $ex->getMessage() . ')',
                ];
                FacturaElectronica::where(['clave_acceso' => $documento['clave_acceso']])->update($datosAct);
            }
            $bar->advance();
        }
        $bar->finish();

        return Command::SUCCESS;
    }
}
