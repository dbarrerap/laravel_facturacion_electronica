<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Factura;
use App\Models\FacturaElectronica;
use DOMDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FacElecGenerar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facelec:generar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generacion de XML para Facturacion Electronica';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $empresa = Empresa::where('id', 1)->first();
        $facturas = FacturaElectronica::query()
        ->with([
            'documento.cliente',
            'documento.detalles',
            'documento.payment',
        ])
        ->where('estado', 'E')
        ->get();
        $bar = $this->output->createProgressBar(count($facturas));

        foreach($facturas as $factura){
            try {
                if (!isset($factura['documento']['cliente']['cf1']) || empty($factura['documento']['cliente']['cf1'])) {
                    Log::warning("Cliente no tiene tipo de identificacion configurada.", ['cliente' => $factura['documento']['cliente']]);
                    throw new \Exception('Cliente no tiene un tipo de identificacion asociado');
                }
                if (!isset($factura['documento']['cliente']['cf2']) || empty($factura['documento']['cliente']['cf2'])) {
                    Log::warning("Cliente no tiene numero de identificacion configurado.", ['cliente' => $factura['documento']['cliente']]);
                    throw new \Exception('Cliente no tiene un numero de documento de identificacion asociado');
                }
                if (Str::lower($factura['documento']['cliente']['cf1']) == 'cedula' && strlen($factura['documento']['cliente']['cf2']) != 10) {
                    Log::warning('Longitud de Cedula no es la correcta.', ['cliente' => $factura['documento']['cliente']]);
                    throw new \Exception('Longitud de Cedula invalida');
                }
                if (Str::lower($factura['documento']['cliente']['cf1']) == 'ruc' && strlen($factura['documento']['cliente']['cf2']) != 13) {
                    Log::warning('Longitud de RUC no es el correcto.', ['cliente' => $factura['documento']['cliente']]);
                    throw new \Exception('Longitud de RUC invalida');
                }

                $xml = new DOMDocument('1.0', 'UTF-8');
                $xml->xmlStandalone = true;
                $xml->preserveWhiteSpace = false;

                $Factura = $xml->createElement('factura');
                $Factura = $xml->appendChild($Factura);
                $domAttribute = $xml->createAttribute('id');
                $domAttribute->value = 'comprobante';
                $Factura->appendChild($domAttribute);
                $domAttribute = $xml->createAttribute('version');
                $domAttribute->value = '1.1.0';
                $Factura->appendChild($domAttribute);

                $infoTributaria = $xml->createElement('infoTributaria');
                $infoTributaria = $Factura->appendChild($infoTributaria);
                $cbc = $xml->createElement('ambiente', 1);
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('tipoEmision', 1);
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('razonSocial', $empresa['razon_social']);
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('nombreComercial', $empresa['razon_social']);
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('ruc', $empresa['ruc']);
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('claveAcceso', $factura['clave_acceso']);
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('codDoc', str_pad('1', 2, '0', STR_PAD_LEFT)/* $factura_doc["tipo"] */);
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('estab', str_pad($factura['establecimiento'], 3, '0', STR_PAD_LEFT));
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('ptoEmi', str_pad($factura['pto_emision'], 3, '0', STR_PAD_LEFT));
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('secuencial', str_pad($factura['fk_factura'], 9, '0', STR_PAD_LEFT));
                $cbc = $infoTributaria->appendChild($cbc);
                $cbc = $xml->createElement('dirMatriz', $empresa['direccion']);
                $cbc = $infoTributaria->appendChild($cbc);

                $infoFactura = $xml->createElement('infoFactura');
                $infoFactura = $Factura->appendChild($infoFactura);
                $cbc = $xml->createElement('fechaEmision', $factura['fecha_emision']);
                $cbc = $infoFactura->appendChild($cbc);
                $cbc = $xml->createElement('obligadoContabilidad', $empresa['obligado_contabilidad']);
                $cbc = $infoFactura->appendChild($cbc);

                $tipoIdentificacion = "07";
                if (Str::lower($factura['documento']['cliente']['cf1']) == "ruc") {
                    $tipoIdentificacion = "04";
                } else if (Str::lower($factura['documento']['cliente']['cf1']) == "cedula") {
                    $tipoIdentificacion = "05";
                } else if (Str::lower($factura['documento']['cliente']['cf1']) == "pasaporte") {
                    $tipoIdentificacion = "06";
                }
                $cbc = $xml->createElement('tipoIdentificacionComprador', $tipoIdentificacion); //ver ficha tecnica SRI tabla 6
                $cbc = $infoFactura->appendChild($cbc);
                $cbc = $xml->createElement('razonSocialComprador', $factura['documento']['cliente']['name']);
                $cbc = $infoFactura->appendChild($cbc);
                $cbc = $xml->createElement('identificacionComprador', $factura['documento']['cliente']['cf2']);
                $cbc = $infoFactura->appendChild($cbc);
                if (isset($factura['documento']['cliente']['address'])) {
                    $cbc = $xml->createElement('direccionComprador', $factura['documento']['cliente']['address']);
                    $cbc = $infoFactura->appendChild($cbc);
                }
                $cbc = $xml->createElement('totalSinImpuestos', number_format(floatVal($factura['documento']['total']), 2, '.', ''));
                $cbc = $infoFactura->appendChild($cbc);
                $cbc = $xml->createElement('totalDescuento', number_format(floatVal($factura['documento']['total_discount']), 2, '.', ''));
                $cbc = $infoFactura->appendChild($cbc);

                $totalConImpuestos = $xml->createElement('totalConImpuestos');
                $totalConImpuestos = $infoFactura->appendChild($totalConImpuestos);
                $totalImpuesto = $xml->createElement('totalImpuesto');
                $totalImpuesto = $totalConImpuestos->appendChild($totalImpuesto);
                $cbc = $xml->createElement('codigo', '2');  //ver ficha tecnica SRI tabla 16 -- 2 IVA, 3 ICE, 5 IRBPNR
                $cbc = $totalImpuesto->appendChild($cbc);
                $cbc = $xml->createElement('codigoPorcentaje', 2); //ver ficha tecnica SRI tabla 17
                $cbc = $totalImpuesto->appendChild($cbc);
                $cbc = $xml->createElement('baseImponible', number_format(floatVal($factura['documento']['total']), 2, '.', ''));
                $cbc = $totalImpuesto->appendChild($cbc);
                $cbc = $xml->createElement('valor', number_format(floatVal($factura['documento']['total_tax']), 2, '.', ''));
                $cbc = $totalImpuesto->appendChild($cbc);
                $cbc = $xml->createElement('propina', '0.00');
                $cbc = $infoFactura->appendChild($cbc);
                $cbc = $xml->createElement('importeTotal', number_format($factura['documento']['grand_total'], 2, '.', ''));
                $cbc = $infoFactura->appendChild($cbc);
                $cbc = $xml->createElement('moneda', 'DOLAR');
                $cbc = $infoFactura->appendChild($cbc);

                $pagos = $xml->createElement('pagos');
                $pagos = $infoFactura->appendChild($pagos);
                $pago = $xml->createElement('pago');
                $pago = $pagos->appendChild($pago);

                if ($factura['documento']['payment']['paid_by'] == 'cash') {
                    $formaPago = '01';
                } else if ($factura['documento']['payment']['paid_by'] == 'gift_card') {
                    $formaPago = '18';
                } else if ($factura['documento']['payment']['paid_by'] == 'cc') {
                    $formaPago = '19';
                } else {
                    $formaPago = '20';
                }
                $cbc = $xml->createElement('formaPago', $formaPago);  //ver ficha tecnica SRI tabla 24
                $cbc = $pago->appendChild($cbc);
                $cbc = $xml->createElement('total', number_format($factura['documento']['grand_total'], 2, '.', ''));
                $cbc = $pago->appendChild($cbc);

                $detalles = $xml->createElement('detalles');
                $detalles = $Factura->appendChild($detalles);
                foreach($factura['documento']['detalles'] as $producto) {
                    $detalle = $xml->createElement('detalle');
                    $detalle = $detalles->appendChild($detalle);
                    $cbc = $xml->createElement('codigoPrincipal', $producto['product_code']);
                    $cbc = $detalle->appendChild($cbc);
                    $cbc = $xml->createElement('descripcion', $producto['product_name']);
                    $cbc = $detalle->appendChild($cbc);
                    $cbc = $xml->createElement('cantidad', number_format(floatVal($producto['quantity']), 2, '.', ''));
                    $cbc = $detalle->appendChild($cbc);
                    $cbc = $xml->createElement('precioUnitario', number_format($producto['net_unit_price'], 2, '.', ''));
                    $cbc = $detalle->appendChild($cbc);
                    $cbc = $xml->createElement('descuento', number_format($producto['discount'], 2, '.', ''));
                    $cbc = $detalle->appendChild($cbc);
                    $totalSinImpuesto = floatval($producto['quantity']) * floatval($producto['net_unit_price']);
                    $cbc = $xml->createElement('precioTotalSinImpuesto', number_format($totalSinImpuesto, 2, ".", ""));
                    $cbc = $detalle->appendChild($cbc);

                    $impuestos = $xml->createElement('impuestos');
                    $impuestos = $detalle->appendChild($impuestos);
                    $impuesto = $xml->createElement('impuesto');
                    $impuesto = $impuestos->appendChild($impuesto);
                    $cbc = $xml->createElement('codigo', '2');
                    $cbc = $impuesto->appendChild($cbc);
                    $cbc = $xml->createElement('codigoPorcentaje', '2');
                    $cbc = $impuesto->appendChild($cbc);
                    $cbc = $xml->createElement('tarifa', 12);
                    $cbc = $impuesto->appendChild($cbc);
                    $cbc = $xml->createElement('baseImponible', floatVal($totalSinImpuesto));
                    $cbc = $impuesto->appendChild($cbc);
                    $cbc = $xml->createElement('valor', number_format($producto['item_tax'], 2, '.', ''));
                    $cbc = $impuesto->appendChild($cbc);
                }
                $xml->formatOutput = true;
                $xml->saveXML();

                $ruta = storage_path('app/comprobantes/generados/');
                $xml->save($ruta . $factura['clave_acceso'] . '.xml');
                //
                $datosAct = [
                    'observaciones' => 'XML generado correctamente.',
                    'estado' => 'G',
                    'mensaje_error' => null,
                ];
                FacturaElectronica::where('id', $factura['id'])->update($datosAct);
            } catch (\Exception $ex) {
                $datosAct = [
                    'mensaje_error' => 'GenerarXML: Error al generar XML (' . $ex->getMessage() . ')',
                ];
                $this->error($factura['clave_acceso'] . ': ' . $ex->getMessage());
                FacturaElectronica::where('id', $factura['id'])->update($datosAct);
            } 
            //
            $bar->advance();
        }
        $bar->finish();

        return Command::SUCCESS;
    }
}
