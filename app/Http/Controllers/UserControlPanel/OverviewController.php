<?php


namespace App\Http\Controllers\UserControlPanel;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityMonitoringReport;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OverviewController  extends Controller
{

    public function index(Request $request): Factory|View|Application
    {
        $sitesQuery =Auth::user()->sites;
        $totalSitesCount = $sitesQuery->count();
        $totalObjectsPagesCount = (int)ceil($totalSitesCount / $this->pageSize);
        $sites = $sitesQuery->toQuery() ->simplePaginate($this->pageSize);
        foreach ($sites as $site){
            $overviewData = $site->getOverview();
            $site->total_pages_count = count($overviewData);
            $site->error_monitored_pages_count = 0;
            $site->success_monitored_pages_count = 0;
            $site->not_monitored_pages_count = 0;
            foreach ($overviewData as $overviewDatum){
                if($overviewDatum===false){
                    $site->error_monitored_pages_count++;
                }
                elseif($overviewDatum===true){
                    $site->success_monitored_pages_count++;
                }
                else{
                    $site->not_monitored_pages_count++;
                }
            }

            $site->last_monitoring_time = '';
            $lastReport = AvailabilityMonitoringReport::getLastReport($site->id);
            if ($lastReport){
                $site->last_monitoring_time = $lastReport->last_monitoring_time;
            }
        }
        if ($request->ajax()) {
            return view(
                'components.overview-table',
                [
                    'rowsData' => $sites,
                    'totalPageCount' => $totalObjectsPagesCount
                ]
            );
        }

        return view(
            'overview.index',
            [
                'rowsData' => $sites,
                'totalPageCount' => $totalObjectsPagesCount
            ]
        );
    }

}
