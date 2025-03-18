<?php

namespace App\Providers;

use App\Console\Commands\TaskEstrategicoTaticoCommand;
use App\Console\Commands\TaskRhLeadTimeCommand;
use App\Console\Commands\TasksMarketingColegioItaquaCommand;
use App\Console\Commands\TasksMarketingEstacaoFuturaCommand;
use App\Console\Commands\TasksMarketingFisk;
use App\Console\Commands\TasksMarketingLiceuCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class TarefaAgendada extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(Schedule $schedule): void
    {
        #php artisan schedule:work 

        # $schedule->command(TaskRhLeadTimeCommand::class);

        # $schedule->command(TaskEstrategicoTaticoCommand::class);
        /*
        $schedule->call(function () {
            \Artisan::call(TaskRhLeadTimeCommand::class);
            \Artisan::call(TaskEstrategicoTaticoCommand::class);
        })->everyMinute();*/

        $schedule->command(TaskRhLeadTimeCommand::class);
        $schedule->command(TaskEstrategicoTaticoCommand::class);

        # $schedule->command(TasksMarketingFisk::class)->everyMinute(); // executa a cada 1 minuto

        # $schedule->command(TasksMarketingEstacaoFuturaCommand::class)->everyMinute(); // executa a cada 1 minuto

        # $schedule->command(TasksMarketingLiceuCommand::class)->everyMinute(); // executa a cada 1 minuto

        # $schedule->command(TasksMarketingColegioItaquaCommand::class)->everyMinute(); // executa a cada 1 minuto

        # $schedule->command(TaskEstrategicoTaticoCommand::class)->everyMinute(); // executa a cada 1 minuto

        /*
       $schedule->call(function () {
        Artisan::call(TasksMarketingFisk::class);
        Artisan::call(TasksMarketingEstacaoFuturaCommand::class);
        Artisan::call(TasksMarketingLiceuCommand::class);
        Artisan::call(TasksMarketingColegioItaquaCommand::class);
    })->everyMinute(); */
    }
}
