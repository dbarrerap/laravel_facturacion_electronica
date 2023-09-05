<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConfigEmpresa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facelec:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->confirm('Desea configurar la Empresa?', true)) {
            return Command::INVALID;
        }

        $nombre_comercial = $this->ask('Ingrese el Nombre Comercial de la Empresa');
        $razon_social = $this->ask('Ingrese la Razon Social de la Empresa');
        $ruc = $this-> ask('Ingrese el RUC de la Empresa');
        $direccion = $this->ask('Ingrese la direccion de la matriz');
        $telefono = $this->ask('Ingrese un Numero Telefonico');
        $email = $this->ask('Ingrese el Correo Electronico');
        $obligado_contabilidad = $this->confirm('Esta obligado a llevar contabilidad?', true);

        try {
            $empresa = new Empresa();
            $empresa->nombre_comercial = $nombre_comercial;
            $empresa->razon_social = $razon_social;
            $empresa->ruc = $ruc;
            $empresa->direccion = $direccion;
            $empresa->telefono = $telefono;
            $empresa->email = $email;
            $empresa->obligado_contabilidad = ($obligado_contabilidad) ? 'SI' : 'NO';
            $empresa->estado = 'A';
            $empresa->save();

            $this->line('Empresa: ' . $nombre_comercial . ' ingresada exitosamente.');
            return Command::SUCCESS;
        } catch (\Exception $ex) {
            $this->error($ex->getMessage());
            Log::error($ex->getMessage());
            return Command::FAILURE;
        }
        // $this->info('');
    }
}
