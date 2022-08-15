<?php

namespace App\Console\Commands;

use App\Models\MonitoringSite;
use App\Parallel\RequestUrlComponent;
use App\Parallel\AvailabilityMonitoringReportComponent;
use Illuminate\Console\Command;
use parallel\{Channel,Runtime};
use PDO;


class AvailabilityMonitoringTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:availability {period}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Availability monitoring';

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
    public function handle(): int
    {
        $period = $this->argument('period');
//        Log::info("$period monitored");

        if($period){
            $timeStart = microtime(true);
            $sites = MonitoringSite::where('monitoring_period',$period)->get();

            $monitoringDate = date('Y-m-d H:i:00');
            $runtime = new Runtime();
            foreach ($sites as $site){
                $argArray = [
                    'id'=>$site->id,
                    'domain'=>$site->domain,
                    'pages'=>$site->getActualPagesArray(),
                    'timeout'=> $site->timeout,
                    'telegram_id'=>$site->chat_id,
                ];
                $runtime->run(function (array $siteData, string $monitoringDate, $timeStart) {
                    require_once '/var/www/html/monitoring/app/Parallel/AvailabilityMonitoringReportComponent.php';
                    require_once '/var/www/html/monitoring/app/Monitoring/TelegramComponent.php';
                    $runtime = new Runtime();
                    $monitoringTasks = [];
                    foreach ($siteData['pages'] as $page){
                        $fullUrl = preg_replace('/([^:])\/\//','$1/',$siteData['domain'].$page);
                        $monitoringTasks[] = $runtime->run(function (string $url, int $timeout, int $id){
                            require_once '/var/www/html/monitoring/app/Parallel/RequestUrlComponent.php';
                            $requestComponent = new RequestUrlComponent(
                                $url,
                                $timeout
                            );
                            $result = $requestComponent->requestToUrl();
                            return ['id'=>$id,'url'=>$url, 'code'=>$requestComponent->getCode(),'message'=>$requestComponent->getErrorMessage()];
                        }, [$fullUrl,$siteData['timeout'], $siteData['id']]);
                    }
                    $runtime->close();
                    $reportComponent = new AvailabilityMonitoringReportComponent($siteData['telegram_id']);
                    foreach ($monitoringTasks as $task){
                        $results = $task->value();
                        $reportComponent->processMonitoringResults($siteData['id'],$results['url'],$results['code'],$monitoringDate,$results['message']);
                        $task->cancel();
                    }
                    echo  (microtime(true) - $timeStart).PHP_EOL;
                }, [$argArray,$monitoringDate,$timeStart]);
            }
        }
        return Command::SUCCESS;
    }
}
