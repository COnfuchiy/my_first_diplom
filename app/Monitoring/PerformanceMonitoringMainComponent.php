<?php


namespace App\Monitoring;

use App\Jobs\PSIRequestJob;
use App\Models\MonitoringSite;
use Exception;
use Illuminate\Support\Facades\Log;

class PerformanceMonitoringMainComponent extends BaseMonitoringComponent
{

    public function init(): void
    {
        if (isset($this->site->sitemap_url)) {
            $this->sitemapMonitoring();
        } else {
            $urlsArray = $this->site->getPagesToArray();
            foreach ($urlsArray as $url) {
                $this->urlMonitoring($url);
            }
        }
    }

    protected function urlMonitoring(string $url)
    {

        if($this->site->seo_psi_desktop_check){
            PSIRequestJob::dispatch($this->site,0,$url,$this->monitoringDatetime);
        }
        if($this->site->seo_psi_mobile_check){
            PSIRequestJob::dispatch($this->site,1,$url,$this->monitoringDatetime);
        }
    }

    protected function sitemapMonitoring()
    {
        $parser = new SitemapParser($this->site->sitemap_url);

        if ($monitoringUrls = $parser->initParse()) {
            Log::info($this->site->sitemap_url . " parsing complete, number urls: " . sizeof($monitoringUrls));

            foreach ($monitoringUrls as $url) {
                $this->urlMonitoring($url);
            }
            return;
        }
        $error = $parser->getError();
        // error log
    }
}
