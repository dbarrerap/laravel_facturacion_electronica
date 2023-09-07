<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PHPJasper\PHPJasper;

class JReportProcesar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jreport:generar {numero : ID de la Factura a generar RIDE} {clave : Clave de Acceso de la Factura}';

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
        $input_file = storage_path('app/reportes/factura.jasper');
        $output_path = storage_path('app/comprobantes/RIDE/' . $this->argument('clave'));
        $options_arr = [ 
            'format' => ['pdf'],
            'params' => [
                'numero' => $this->argument('numero')
            ], 
            'db_connection' => [
                'driver' => 'mysql',
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'host' => env('DB_HOST'),
                'database' => env('DB_DATABASE'),
                'port' => env('DB_PORT'),
                'jdbc_driver' => 'mariadb-java-client-2.7.2',
                'jdbc_url' => "jdbc:mariadb://" . env('DB_HOST') . ":" . env('DB_PORT') . ";databaseName=" . env('DB_DATABASE')
            ]
        ];

        $jasper = new PHPJasper;

        /* dd($jasper->process(
            $input_file,
            $output_path,
            $options_arr,
        )->output()); */
        $jasper->process(
            $input_file,
            $output_path,
            $options_arr,
        )->execute();

        return Command::SUCCESS;
    }
}
