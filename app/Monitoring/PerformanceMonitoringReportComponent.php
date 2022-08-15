<?php


namespace App\Monitoring;

use App\Models\MonitoringSite;
use App\Models\PerformanceMonitoringReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringReportComponent
{


    public function __construct(
        private MonitoringSite $site,
    ) {
    }

    public function checkReportsForClean()
    {
        $cleanReportPeriod = $this->site->performance_report_clear_num_days;
        if (!$cleanReportPeriod) {
            $cleanReportPeriod = config('performance_report_clear_num_days');
        }
        $reports = PerformanceMonitoringReport::where(
            'monitoring_time',
            '>',
            now()->addDays($cleanReportPeriod)
        );
        if ($reports->count()) {
            $reports->delete();
        }
    }

    public function setupReport(string $url,PSIMonitoringData $PSIData, Carbon|string $datetime = '', array $metaData = null): bool
    {
        $url = $this->getPathOrFullUrl($url);
        $report = new PerformanceMonitoringReport(
            [
                'site_id' => $this->site->id,
                'path' => $url,
                'monitoring_time' => $datetime!=='' ?$datetime: now()
            ]
        );
        $report->setupFromPSIData($PSIData);
        if ($metaData) {
            $report->setupFromMetaData($metaData);
        }
        print("success report $url");
        // compare total score and notify
        return $report->save();
    }

    private function getPathOrFullUrl($url)
    {
        $urlData = parse_url($url);
        if ($urlData['host'] !== $this->site->domain) {
            return $urlData['path'];
        }
        return $url;
    }

}
