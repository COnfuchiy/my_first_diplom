<?php

namespace App\Jobs;

use App\Models\MonitoringSite;
use App\Monitoring\AvailabilityMonitoringMainComponent;
use App\Monitoring\PerformanceMonitoringMainComponent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PerformanceMonitoringJob implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public MonitoringSite $site;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        MonitoringSite $site
    )
    {
        $this->site = $site->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->site->seo_psi_desktop_check || $this->site->seo_psi_mobile_check){
            self::dispatch($this->site)->delay(now()->addDays($this->site->seo_psi_period_num_days));
        }
        (new PerformanceMonitoringMainComponent($this->site,now()))->init();
    }
    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->site->id;
    }
}
