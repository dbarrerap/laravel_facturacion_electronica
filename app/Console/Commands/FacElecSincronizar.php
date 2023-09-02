<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Factura;
use App\Models\FacturaElectronica;
use Illuminate\Console\Command;

class FacElecSincronizar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facelec:sincronizar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisar si hay facturas para generar XML';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $empresa = Empresa::where('id', 1)->first();
        $facturas_qb = Factura::with('pos_settings')->where(function ($query) {
            $query->where('note', '')
                ->orWhereNull('note');
        });
        $facturas = $facturas_qb->get();

        $bar = $this->output->createProgressBar(count($facturas));

        foreach($facturas as $factura) {
            $secuencial = explode('/', $factura['reference_no']);
            $claveAcceso = \Carbon\Carbon::parse($factura['date'])->format('dmY');
            $claveAcceso .= str_pad('1', 2, '0', STR_PAD_LEFT);  // Tipo Documento: 1 Factura, 6 Guia Remision -- 2 de longitud
            $claveAcceso .= $empresa->ruc;  // RUC de la empresa -- 13 de longitud
            $claveAcceso .= '1'; // Tipo de Ambiente: 1 Desarrollo, 2 Produccion -- 1 de longitud
            $claveAcceso .= str_pad($factura['pos_settings']['cf_value1'], 3, '0', STR_PAD_LEFT);  // Establecimiento -- 3 de longitud
            $claveAcceso .= str_pad($factura['pos_settings']['cf_value2'], 3, '0', STR_PAD_LEFT);  // Punto Emision -- 3 de longitud
            $claveAcceso .= str_pad($secuencial[count($secuencial) - 1], 9, '0', STR_PAD_LEFT);  // Secuencial -- 9 de longitud
            $claveAcceso .= '12345678'; //Codigo Numerico: segun la ficha tecnica del sri por defecto es 12345678 --- 8 de longitud
            $claveAcceso .= '1'; //Tipo Emision: segun la ficha tecnica del sri el codigo por defecto es 1 --- 1 de longitud
            $claveAcceso .= $this->getMod11Dv($claveAcceso);

            $fe = new FacturaElectronica();
            $fe->fk_factura = $factura['id'];
            $fe->num_documento = $factura['reference_no'];
            $fe->fecha_emision = \Carbon\Carbon::parse($factura['date'])->format('d/m/Y');
            $fe->establecimiento = $factura['pos_settings']['cf_value1'];
            $fe->pto_emision = $factura['pos_settings']['cf_value2'];
            $fe->estado = 'E';
            $fe->clave_acceso = $claveAcceso;
            $fe->save();

            $bar->advance();
        }
        $bar->finish();
        $facturas_qb->update(['note' => 'FacElec Generado']);

        return Command::SUCCESS;
    }

    function getMod11Dv($num)
    {
        $digits = str_replace(array('.', ','), array('' . ''), strrev($num));
        if (!ctype_digit($digits)) return false;

        $sum = 0;
        $factor = 2;
        for ($i = 0; $i < strlen($digits); $i++) {
            $sum += substr($digits, $i, 1) * $factor;
            if ($factor == 7) $factor = 2;
            else $factor++;
        }
        $dv = 11 - ($sum % 11);
        if ($dv == 10) return 1;
        if ($dv == 11) return 0;
        return $dv;
    }
}
