<?php


namespace App\Monitoring;
use App\Models\MonitoringSite;
use Illuminate\Console\Scheduling\Schedule;

class MonitoringJobsDistributor
{
    private static string $availabilityTaskCommand = 'monitoring:availability';

    private static string $performanceTaskCommand = 'monitoring:performance';

    public static function setupAvailabilityMonitoringTasks(Schedule $schedule){
        $allMonitoringPeriods = MonitoringSite::getAllMonitoringPeriod();
        foreach ($allMonitoringPeriods as $period){
            $schedule->exec('cd '.base_path().' && '.env('PHP_ZTS_PATH').' artisan '.self::$availabilityTaskCommand." $period &")->cron("*/$period * * * *");
        }
    }
    public static function setupPerformanceMonitoringTasks(Schedule $schedule){
        $schedule->exec('cd '.base_path().' && php artisan queue:work --stop-when-empty --timeout=0 &')->monthly();
        $schedule->exec('cd '.base_path().' && php artisan '.self::$performanceTaskCommand.' &')->monthly();
    }
}
