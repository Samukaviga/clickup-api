<?php

namespace App\Providers;

use App\Console\Commands\SyncClickUpData;
use App\Console\Commands\TaskRhLeadTimeCommand;
use App\Console\Commands\TasksMarketingColegioItaquaCommand;
use App\Console\Commands\TasksMarketingEstacaoFuturaCommand;
use App\Console\Commands\TasksMarketingFisk;
use App\Console\Commands\TasksMarketingLiceuCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;

class TarefaAgendada extends ServiceProvider
{
    
    public function register(): void
    {
        //
    }

    public function boot(Schedule $schedule): void
    {
        #php artisan schedule:work 



        $schedule->command(TaskRhLeadTimeCommand::class)->everyMinute(); // executa a cada 1 minuto
        
        $schedule->command(TasksMarketingFisk::class)->everyMinute(); // executa a cada 1 minuto
        
        $schedule->command(TasksMarketingEstacaoFuturaCommand::class)->everyMinute(); // executa a cada 1 minuto
        
        $schedule->command(TasksMarketingLiceuCommand::class)->everyMinute(); // executa a cada 1 minuto
        
        $schedule->command(TasksMarketingColegioItaquaCommand::class)->everyMinute(); // executa a cada 1 minuto
        
       /*
       $schedule->call(function () {
        Artisan::call(TasksMarketingFisk::class);
        Artisan::call(TasksMarketingEstacaoFuturaCommand::class);
        Artisan::call(TasksMarketingLiceuCommand::class);
        Artisan::call(TasksMarketingColegioItaquaCommand::class);
    })->everyMinute(); */
       
    }
}
