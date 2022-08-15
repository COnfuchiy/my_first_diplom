<?php

namespace App\Console;

use App\Console\Commands\AvailabilityMonitoringTask;
use App\Console\Commands\PerformanceMonitoringTask;
use App\Monitoring\MonitoringJobsDistributor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AvailabilityMonitoringTask::class,
        PerformanceMonitoringTask::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        MonitoringJobsDistributor::setupAvailabilityMonitoringTasks($schedule);
        MonitoringJobsDistributor::setupPerformanceMonitoringTasks($schedule);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {

    }
}
