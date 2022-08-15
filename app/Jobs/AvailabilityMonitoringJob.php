<?php

namespace App\Jobs;

use App\Models\MonitoringSite;
use App\Monitoring\PerformanceMonitoringJobsDistributor;
use App\Monitoring\AvailabilityMonitoringMainComponent;
use App\Monitoring\PerformanceMonitoringJobsDistributor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AvailabilityMonitoringJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public int $period
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $monitoringSites = MonitoringSite::orWhere('domain_period_num_minutes', $this->period)
            ->orWhere('page_period_num_minutes', $this->period)
            ->where('is_active', true)
            ->get();
        if (!sizeof($monitoringSites)) {
            return;
        }
        Loop::run(function () use($monitoringSites,$period) {
            foreach ($monitoringSites as $site) {
                (new AvailabilityMonitoringMainComponent($site, $this->period))->init();
            }
        });
        Log::build(
            [
                'driver' => 'daily',
                'path' => storage_path("logs/monitoring/availability/$this->period/handle.log"),
            ]
        )->info("availability monitoring handle success: " . sizeof($monitoringSites) . " sites");
        PerformanceMonitoringJobsDistributor::resumeJob($this->period);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
//    public function uniqueId(): string
//    {
//        return $this->period;
//    }
}
