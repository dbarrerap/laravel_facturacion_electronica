<?php

namespace App\Console\Commands;

use App\Models\FacturaElectronica;
use Illuminate\Console\Command;
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
                $url = "java -jar " . storage_path('app/firmador/FirmaElectronica.jar') . " " . storage_path('app/firmador/certificado.p12') . " " . env('CLAVE_CERTIFICADO') . " " . storage_path('app/comprobantes/generados/') . $documento['clave_acceso'] . ".xml " . storage_path('app/comprobantes/firmados/') . " " . $documento['clave_acceso'] . ".xml";
                exec($url, $o);

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
                    'mensaje_error' => 'FirmarXML: Error al generar al firmar XML (' . $ex->getMessage() . ')',
                ];
                FacturaElectronica::where(['clave_acceso' => $documento['clave_acceso']])->update($datosAct);
            }
            $bar->advance();
        }
        $bar->finish();

        return Command::SUCCESS;
    }
}
