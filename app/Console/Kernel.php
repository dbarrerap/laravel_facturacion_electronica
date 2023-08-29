<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('facelec:sincronizar')->hourly();
        $schedule->command('facelec:generar')->hourlyAt(5);
        $schedule->command('facelec:firmar')->hourlyAt(10);
        $schedule->command('facelec:enviar')->hourlyAt(15);
        $schedule->command('facelec:autorizados')->hourlyAt(25);
        $schedule->command('facelec:correo')->hourlyAt(35);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
