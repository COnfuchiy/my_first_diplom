<?php

namespace App\Http\Controllers\UserControlPanel;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityMonitoringReport;
use App\Models\DataTableModel;
use App\Models\PerformanceMonitoringReport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Util\Json;

class PerformanceReportController extends Controller
{
    use DataTableModel;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $reportsQuery = PerformanceMonitoringReport::getAllUserReports(Auth::user()->id);
        return $this->processingDataTable(
            'performance-reports',
            $request,
            $reportsQuery,
            PerformanceMonitoringReport::class,
            'monitoring_date',
            'desc'
        );
    }


    /**
     * Display the specified resource.
     *
     * @param PerformanceMonitoringReport $performanceReport
     * @return View|Factory|Application
     */
    public function show(PerformanceMonitoringReport $performanceReport): View|Factory|Application
    {
        $reportSiteFullUrl = $performanceReport->site->domain . substr_replace($performanceReport->path, '', 0, 1);
        $performanceReport->url = $reportSiteFullUrl;
        return view('performance-reports.show',['report'=>$performanceReport]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function massDestroy($id)
    {
        //
    }

}
