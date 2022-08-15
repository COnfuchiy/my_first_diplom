<?php

namespace App\Console\Commands;

use App\Jobs\PSIRequestJob;
use App\Models\MonitoringSite;
use App\Models\PerformanceMonitoringReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PerformanceMonitoringTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:performance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performance monitoring';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sites = MonitoringSite::getSitesToPerformanceMonitoring();
        $monitoringTime = now();
        foreach ($sites as $site){
            $pages = $site->getActualPagesArray();
            foreach ($pages as $page){
                if($site->seo_psi_desktop_check){
                    PSIRequestJob::dispatch($site,PerformanceMonitoringReport::DESKTOP_STRATEGY,$page,$monitoringTime);
                }
                if($site->seo_psi_mobile_check){
                    PSIRequestJob::dispatch($site,PerformanceMonitoringReport::MOBILE_STRATEGY,$page,$monitoringTime);
                }
            }
        }
//        Artisan::call('queue:work',['--stop-when-empty'=>true, '--timeout'=>0]);
        return Command::SUCCESS;
    }
}
