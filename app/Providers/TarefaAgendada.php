<?php

namespace App\Providers;

use App\Console\Commands\SyncClickUpData;
use App\Console\Commands\TasksMarketingLiceuCommand;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class TarefaAgendada extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Schedule $schedule): void
    {
       // $schedule->command(SyncClickUpData::class)->everyMinute(); // executa a cada 1 minuto
        $schedule->command(TasksMarketingLiceuCommand::class)->everyMinute(); // executa a cada 1 minuto
       
    }
}
