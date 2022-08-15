<?php

namespace App\Jobs;

use App\Models\MonitoringSite;
use App\Monitoring\PageSpeedInsightsComponent;
use App\Monitoring\PerformanceMonitoringReportComponent;
use App\Parallel\RequestUrlComponent;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PSIRequestJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private MonitoringSite $site;

    private string $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        MonitoringSite $site,
        private int $requestStrategy,
        string $path,
        private Carbon $monitoringDatetime
    ) {
        $this->url = $site->getFullUrl($path);
        $this->site = $site->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $PSIComponent = new PageSpeedInsightsComponent(
                $this->url,
                $this->requestStrategy,
                config('monitoring.psi_api_key')
            );
            $PSIData = $PSIComponent->analyseUrl();


            if ($this->site->meta_check) {
                $requestComponent = new RequestUrlComponent(
                    $this->url,
                    $this->site->timeout,
                    true
                );

                if ($requestComponent->requestToUrl()) {
                    $metaData = $requestComponent->metaData;
                } else {
                    // error log
                }
            }

            $reportComponent = new PerformanceMonitoringReportComponent(
                $this->site
            );
            $reportComponent->setupReport(
                $this->url,
                $PSIData,
                $this->monitoringDatetime,
                $metaData ?? null
            );

        } catch (Exception $e) {
            // error log
            return;
        }
    }

    public function uniqueId()
    {
        return $this->url;
    }
}
