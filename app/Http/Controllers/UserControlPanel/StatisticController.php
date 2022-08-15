<?php


namespace App\Http\Controllers\UserControlPanel;

use App\Http\Controllers\Controller;
use App\Models\MonitoringSite;
use App\Models\PerformanceMonitoringReport;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;

class StatisticController extends Controller
{

    public int $countViewingAvailabilityMonitoringTimes = 10;
    public int $countViewingPagesMonitoringTimes = 20;
    public int $countViewingPsiMonitoringTimes = 9;


    public function index(Request $request, int $siteId)
    {
        $site = MonitoringSite::find($siteId);

        $validated = $request->validate(
            [
                'page' => 'nullable|integer',
                'dateFrom' => 'nullable|date',
                'dateTo' => 'nullable|date',
            ]
        );

        $sitePages = $site->getActualPagesArray();

        if (isset($validated['dateFrom']) && isset($validated['dateTo'])) {
            $dateFrom = Carbon::createFromTimeString($validated['dateFrom'])->toImmutable();
            $dateTo = Carbon::createFromTimeString($validated['dateTo'])->toImmutable();
        } else {
            $dateTo = now()->toImmutable();
            $dateFrom = $dateTo->subDays(2);
        }

        $timeInterval = $dateTo->diffForHumans($dateFrom,['syntax' => CarbonInterface::DIFF_ABSOLUTE,'parts'=>-1]);

        $siteStatistic = $site->getAvailabilityChartDataset($dateFrom,$dateTo);

        $monitoringCount = $siteStatistic->count();

        $totalPageCount = (int)ceil($monitoringCount / $this->countViewingAvailabilityMonitoringTimes);

        if (count($siteStatistic) > $this->countViewingAvailabilityMonitoringTimes) {
            if (isset($validated['page']) && $validated['page'] > 1) {
                $siteStatistic = $siteStatistic->forPage(
                    $validated['page'],
                    $this->countViewingAvailabilityMonitoringTimes
                );
            } else {
                $siteStatistic = $siteStatistic->forPage(0, $this->countViewingAvailabilityMonitoringTimes);
            }
        }

        $labels = array_keys($siteStatistic->all());
        $successData = [];
        $errorData = [];
        foreach ($siteStatistic as $timeStatistic) {
            $successData[] = $timeStatistic['successCountReports'];
            $errorData[] = $timeStatistic['errorCountReports'];
        }

        $currentPage = isset($validated['page']) ? (integer)$validated['page']: 1;
        $view = 'statistic.index';
        if ($request->ajax()) {
            $view = 'components.statistic.availability-statistic-chart';
        }
        return view(
            $view,
            compact(
                'site',
                'sitePages',
                'labels',
                'errorData',
                'successData',
                'timeInterval',
                'monitoringCount',
                'currentPage',
                'totalPageCount'
            )
        );
    }

    public function page(Request $request, int $siteId)
    {
        if ($request->ajax()) {
            $site = MonitoringSite::find($siteId);
            $validated = $request->validate(
                [
                    'page' => 'required|integer',
                    'dateFrom' => 'nullable|date',
                    'dateTo' => 'nullable|date',
                    'sitePage' => 'required|string',
                ]
            );

            if ($validated['dateFrom'] && $validated['dateTo']) {
                $dateFrom = Carbon::createFromTimeString($validated['dateFrom'])->toImmutable();
                $dateTo = Carbon::createFromTimeString($validated['dateTo'])->toImmutable();
            } else {
                $dateTo = now()->toImmutable();
                $dateFrom = $dateTo->subDays(2);
            }

            $timeInterval = $dateTo->diffForHumans($dateFrom,['syntax' => CarbonInterface::DIFF_ABSOLUTE,'parts'=>-1]);

            $pageStatistic = $site->getPagesChartDataset($validated['sitePage'], $dateTo, $dateFrom);

            $monitoringCount = $pageStatistic->count();

            $totalPageCount = (int)ceil($monitoringCount / $this->countViewingPagesMonitoringTimes);

            if ($monitoringCount > $this->countViewingPagesMonitoringTimes) {
                if ($validated['page'] && $validated['page'] > 1) {
                    $pageStatistic = $pageStatistic->forPage(
                        $validated['page'],
                        $this->countViewingPagesMonitoringTimes
                    );
                } else {
                    $pageStatistic = $pageStatistic->forPage(0, $this->countViewingPagesMonitoringTimes);
                }
            }


            $pageStatisticArray = $pageStatistic->all();


            $labels = array_keys($pageStatisticArray);
            $pageData = array_column($pageStatisticArray,'code');
            $errorMessages = array_column($pageStatisticArray,'message');

            $currentPage = (integer)$validated['page'];
            return view(
                'components.statistic.pages-statistic-chart',
                compact(
                    'labels',
                    'pageData',
                    'timeInterval',
                    'errorMessages',
                    'monitoringCount',
                    'currentPage',
                    'totalPageCount'
                )
            );
        }
        return redirect()->action([$this::class, 'index'],['siteId'=>$siteId]);
    }

    public function psi(Request $request, int $siteId)
    {
        if ($request->ajax()) {
            $site = MonitoringSite::find($siteId);
            $validated = $request->validate(
                [
                    'page' => 'required|integer',
                    'dateFrom' => 'nullable|date',
                    'dateTo' => 'nullable|date',
                    'sitePage' => 'required|string',
                ]
            );

            if ($validated['dateFrom'] &&
                $validated['dateTo']) {
                $dateFrom = Carbon::createFromTimeString($validated['dateFrom'])->toImmutable();
                $dateTo = Carbon::createFromTimeString($validated['dateTo'])->toImmutable();
            } else {
                $dateTo = now()->toImmutable();
                $dateFrom = $dateTo->subMonths(6);
            }

            $timeInterval = $dateTo->diffForHumans($dateFrom,['syntax' => CarbonInterface::DIFF_ABSOLUTE,'parts'=>-1]);

            $psiStatistic = $site->getPsiChartDataset($validated['sitePage'], $dateTo, $dateFrom);

            $monitoringCount = $psiStatistic->count();

            $totalPageCount = (int)ceil($monitoringCount / $this->countViewingPsiMonitoringTimes);
            if ($monitoringCount > $this->countViewingPsiMonitoringTimes) {
                if ($validated['page'] && $validated['page'] > 1) {
                    $psiStatistic = $psiStatistic->forPage(
                        $validated['page'],
                        $this->countViewingPsiMonitoringTimes
                    );
                } else {
                    $psiStatistic = $psiStatistic->forPage(0, $this->countViewingPsiMonitoringTimes);
                }
            }

            $psiStatisticArray = $psiStatistic->all();
            $labels = array_keys($psiStatisticArray);
            $psiDesktopTotalScore = [];
            $psiMobileTotalScore = [];

            $psiMobileData = [];
            $psiDesktopData = [];

            foreach ($psiStatisticArray as $data) {
                $psiDesktopTotalScore[] = $data['desktop']['total_score'] ??  null;
                $psiMobileTotalScore[] = $data['mobile']['total_score'] ??  null;
                $psiMobileData[] = $data['mobile'] ?? [];
                $psiDesktopData[] = $data['desktop'] ?? [];
            }


            $currentPage = (integer)$validated['page'];
            return view(
                'components.statistic.psi-statistic-chart',
                compact(
                    'labels',
                    'psiDesktopTotalScore',
                    'site',
                    'psiMobileTotalScore',
                    'psiDesktopData',
                    'psiMobileData',
                    'timeInterval',
                    'monitoringCount',
                    'currentPage',
                    'totalPageCount'
                )
            );
        }
        return redirect()->action([$this::class, 'index']);
    }
}
