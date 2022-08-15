<?php


namespace App\Monitoring;


use App\Models\MonitoringSite;
use Carbon\Carbon;

abstract class BaseMonitoringComponent
{

    public function __construct(
        protected MonitoringSite $site,
        protected Carbon $monitoringDatetime
    ) {
    }

    abstract public function init(): void;

    abstract protected function urlMonitoring(string $url);

    abstract protected function sitemapMonitoring();

}
