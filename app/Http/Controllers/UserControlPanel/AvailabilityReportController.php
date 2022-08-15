<?php

namespace App\Http\Controllers\UserControlPanel;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityMonitoringReport;
use App\Models\DataTableModel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityReportController extends Controller
{
    use DataTableModel;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request
    ): Factory|View|Application {
        $reports = AvailabilityMonitoringReport::getAllUserReports(Auth::user()->id);
        return $this->processingDataTable(
            'availability-reports',
            $request,
            $reports->toQuery(),
            AvailabilityMonitoringReport::class,
            'last_date',
            'desc'
        );
    }


    /**
     * Display the specified resource.
     *
     * @param AvailabilityMonitoringReport $availabilityReport
     * @return Application|Factory|View
     */
    public function show(AvailabilityMonitoringReport $availabilityReport): View|Factory|Application
    {
        $reportSiteFullUrl = $availabilityReport->site->domain . substr_replace($availabilityReport->path, '', 0, 1);
        $availabilityReport->url = $reportSiteFullUrl;
        return view('availability-reports.show',['report'=>$availabilityReport]);
    }
}
