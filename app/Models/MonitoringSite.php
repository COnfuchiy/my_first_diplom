<?php

namespace App\Models;

use App\Monitoring\SitemapParser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use JetBrains\PhpStorm\Pure;


/**
 * Class MonitoringSite
 * @property int id
 * @property int user_id
 * @property string domain
 * @property null|int sitemap_url
 * @property int monitoring_period
 * @property int timeout
 * @property bool ssl_check
 * @property null|int ssl_notify_num_days
 * @property bool seo_psi_mobile_check
 * @property bool seo_psi_desktop_check
 * @property null|int seo_psi_period_num_days
 * @property null|int seo_psi_mobile_min_value
 * @property null|int seo_psi_desktop_min_value
 * @property bool meta_check
 * @property int chat_id
 * @property null|int availability_report_clear_num_days
 * @property null|int performance_report_clear_num_days
 * @property bool is_active
 * @package App\Models
 */
class MonitoringSite extends Model implements DataSortable
{
    use HasFactory;

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'ssl_check' => false,
        'meta_check' => false,
        'seo_psi_mobile_check' => false,
        'seo_psi_desktop_check' => false,
        'is_active' => true,
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'domain',
        'sitemap_url',
        'monitoring_period',
        'timeout',
        'ssl_check',
        'ssl_notify_num_days',
        'seo_psi_mobile_check',
        'seo_psi_desktop_check',
        'seo_psi_period_num_days',
        'seo_psi_mobile_min_value',
        'seo_psi_desktop_min_value',
        'meta_check',
        'chat_id',
        'availability_report_clear_num_days',
        'performance_report_clear_num_days',
        'is_active',
    ];

    public function __construct(array $attributes = [])
    {
        $this->attributes['timeout'] = config('monitoring.timeout_request');
        $this->attributes['monitoring_period'] = config('monitoring.monitoring_period');
        $this->attributes['ssl_notify_num_days'] = config('monitoring.ssl_notify_num_days');
        $this->attributes['seo_psi_period_num_days'] = config('monitoring.seo_psi_period_num_days');
        $this->attributes['seo_psi_mobile_min_value'] = config('monitoring.seo_psi_mobile_min_value');
        $this->attributes['seo_psi_desktop_min_value'] = config('monitoring.seo_psi_desktop_min_value');
        $this->attributes['availability_report_clear_num_days'] = config('monitoring.monitoring_report_clear_num_days');
        $this->attributes['performance_report_clear_num_days'] = config('monitoring.performance_report_clear_num_days');
        parent::__construct($attributes);
    }

    public static function filter(Builder $query, string $sortColumn, string $sortType): Builder
    {
        if ($sortColumn === 'availability') {
            return $query->orderBy('is_active', $sortType);
        } elseif ($sortColumn === 'date') {
            return $query->orderBy('created_at', $sortType);
        } else {
            return $query->orderBy('domain', $sortType);
        }
    }

    /**
     * @param Builder $query
     * @param array $searchRequest
     * @return Builder
     */
    public static function search(Builder $query, array $searchRequest): Builder
    {
        if ($searchRequest['searchRequest']) {
            $query = $query->where('domain', 'like', '%' . $searchRequest['searchRequest'] . '%');
        }
        return $query;
    }

    /**
     * @param array $requestParams
     * @return bool
     */
    public static function validateFilterParams(array $requestParams): bool
    {
        $validator = Validator::make(
            $requestParams,
            [
                'sortColumn' => 'in:domain,date',
                'sortType' => 'in:asc,desc'
            ]
        );
        return $validator->fails();
    }

    /**
     * @param array $requestParams
     * @return bool
     */
    public static function validateSearchParams(array $requestParams): bool
    {
        $validator = Validator::make(
            $requestParams,
            [
                'searchRequest' => 'nullable|string',
            ]
        );
        return $validator->fails();
    }

    public static function getAllMonitoringPeriod()
    {
        return self::select('monitoring_period')->distinct()->pluck('monitoring_period');
    }

    public static function getSitesToPerformanceMonitoring()
    {
        return self::where('seo_psi_mobile_check', 1)->orWhere('seo_psi_desktop_check', 1)->get();
    }

    #[Pure] public function getPagesToArray(): array
    {
        $outputArray = [];
        foreach ($this->pages as $page) {
            if ($page->path !== '/' && $page->path[0] === '/') {
                $outputArray[] = $this->domain . substr_replace($page->path, '', 0, 1);
            } else {
                $outputArray[] = $this->domain . ($page->path !== '/' ? $page->path : '');
            }
        }
        return $outputArray;
    }

    public function updatePages($urls)
    {
        $sitePages = $this->pages->get('path')->toArray();
        foreach ($sitePages as &$page) {
            $page = $page['path'];
        }
        $allUrls = array_unique(array_merge($sitePages, $urls));
        $addedUrls = array_diff($allUrls, $sitePages);
        $deletedUrls = array_diff($allUrls, $urls);
        foreach ($addedUrls as $newUrl) {
            $sitePage = new Page(['site_id' => $this->id, 'path' => parse_url($newUrl, PHP_URL_PATH)]);
            if (!$sitePage->save()) {
                //
            }
        }
        foreach ($deletedUrls as $unsetUrl) {
            $sitePage = $this->pages->where('path', $unsetUrl)->first();
            if ($sitePage) {
                $sitePage->delete();
            }
        }
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'site_id');
    }

    public function telegramChat(): BelongsTo
    {
        return $this->belongsTo(TelegramChats::class, 'chat_id');
    }

    public function getAvailabilityMonitoringPeriods(): array
    {
        $monitoringPeriods = array($this->domain_period_num_minutes, $this->page_period_num_minutes);
        if ($monitoringPeriods[0] === $monitoringPeriods[1]) {
            unset($monitoringPeriods[1]);
        }
        return $monitoringPeriods;
    }

    public function deleteAllPages()
    {
        if (count($this->pages)) {
            $this->pages->toQuery()->delete();
        }
    }

    public function getOverview(): array
    {
        $urlsArray = $this->getActualPagesArray();
        $outputArray = [];
        foreach ($urlsArray as $page) {
            $outputArray[$page] = AvailabilityMonitoringReport::getUrlStatus($page, $this->id);
        }
        return $outputArray;
    }

    public function getActualPagesArray(): array
    {
        $outputUrlsArray = [];
        if (isset($this->sitemap_url)) {
            $parser = new SitemapParser($this->sitemap_url);

            if ($monitoringUrls = $parser->initParse()) {
                foreach ($monitoringUrls as $url) {
                    $outputUrlsArray[] = parse_url($url, PHP_URL_PATH);
                }
            } else {
                Log::error($parser->getError());
            }
        } else {
            foreach ($this->pages as $page) {
                $outputUrlsArray[] = $page->path;
            }
        }
        if (array_search('/', $outputUrlsArray) === false) {
            $outputUrlsArray[] = '/';
        }
        return $outputUrlsArray;
    }

    public function getAvailabilityChartDataset(
        Carbon|string $dateFrom = '',
        Carbon|string $dateTo = ''
    ): Collection {
        // get all reports from db
        $reports = AvailabilityMonitoringReport::getAllReportsByDate(
            $this->id,
            $dateTo,
            $dateFrom
        );
        $dateStatistic = [];

        $reports->each(
            function ($report) use (&$dateStatistic, $dateTo, $dateFrom) {
                // analysis report for successive reports

                if ($report->monitoring_sequence > 1) {
                    $monitoringSequence = $report->monitoring_sequence;
                    $reportLastTime = Carbon::createFromTimeString($report->last_monitoring_time)->toImmutable();
                    $reportFirstTime = Carbon::createFromTimeString($report->first_monitoring_time)->toImmutable();

                    // if the last monitoring time is greater than date before
                    if ($reportLastTime >= $dateTo) {
                        // add monitoring time in array $i = 0 for first time entry
                        for ($i = 1; $i <= $monitoringSequence; $i++) {
                            $nextTime = $reportFirstTime->addMinutes(($i - 1) * $this->monitoring_period);
                            if ($nextTime <= $dateTo) {
                                $stringNextTime = $nextTime->toDateTimeString();

                                if (!key_exists($stringNextTime, $dateStatistic)) {
                                    $dateStatistic[$stringNextTime] = [
                                        'successCountReports' => 0,
                                        'errorCountReports' => 0
                                    ];
                                }
                                if ($report->http_code >= 200 && $report->http_code < 400) {
                                    $dateStatistic[$stringNextTime]['successCountReports']++;
                                } else {
                                    $dateStatistic[$stringNextTime]['errorCountReports']++;
                                }
                            }
                        }
                    } else {
                        // add monitoring time in array $i = 0 for last time entry
                        for ($i = $monitoringSequence; $i > 0; $i--) {
                            $prevTime = $reportLastTime->subMinutes(($i - 1) * $this->monitoring_period);
                            if ($prevTime >= $dateFrom) {
                                $stringPrevTime = $prevTime->toDateTimeString();

                                if (!key_exists($stringPrevTime, $dateStatistic)) {
                                    $dateStatistic[$stringPrevTime] = [
                                        'successCountReports' => 0,
                                        'errorCountReports' => 0
                                    ];
                                }
                                if ($report->http_code >= 200 && $report->http_code < 400) {
                                    $dateStatistic[$stringPrevTime]['successCountReports']++;
                                } else {
                                    $dateStatistic[$stringPrevTime]['errorCountReports']++;
                                }
                            }
                        }
                    }
                } else {
                    if (!key_exists($report->last_monitoring_time, $dateStatistic)) {
                        $dateStatistic[$report->last_monitoring_time] = [
                            'successCountReports' => 0,
                            'errorCountReports' => 0
                        ];
                    }
                    if ($report->http_code >= 200 && $report->http_code < 400) {
                        $dateStatistic[$report->last_monitoring_time]['successCountReports']++;
                    } else {
                        $dateStatistic[$report->last_monitoring_time]['errorCountReports']++;
                    }
                }
            }
        );
        return collect($dateStatistic)->sortKeysDesc();
    }

    public function getPagesChartDataset($page, $dateTo = '', $dateFrom = ''): \Illuminate\Support\Collection
    {
        // get all reports from db
        $reports = AvailabilityMonitoringReport::getAllReportsByDate(
            $this->id,
            $dateTo,
            $dateFrom,
            AvailabilityMonitoringReport::SPECIFY_PAGES,
            $page
        );

        $pageStatistic = [];

        $reports->each(
            function ($report) use (&$pageStatistic, $dateTo, $dateFrom) {
                // analysis report for successive reports

                if ($report->monitoring_sequence > 1) {
                    $monitoringSequence = $report->monitoring_sequence;
                    $reportLastTime = Carbon::createFromTimeString($report->last_monitoring_time)->toImmutable();
                    $reportFirstTime = Carbon::createFromTimeString($report->first_monitoring_time)->toImmutable();

                    // if the last monitoring time is greater than date before
                    if ($reportLastTime >= $dateTo) {
                        // add monitoring time in array $i = 0 for first time entry
                        for ($i = 1; $i <= $monitoringSequence; $i++) {
                            $nextTime = $reportFirstTime->addMinutes(($i - 1) * $this->monitoring_period);
                            if ($nextTime <= $dateTo) {
                                $stringNextTime = $nextTime->toDateTimeString();
                                $pageStatistic[$stringNextTime] = [
                                    'code' => $report->http_code,
                                    'message' => $report->message
                                ];
                            }
                        }
                    } else {
                        // add monitoring time in array $i = 0 for last time entry
                        for ($i = $monitoringSequence; $i > 0; $i--) {
                            $prevTime = $reportLastTime->subMinutes(($i - 1) * $this->monitoring_period);
                            if ($prevTime >= $dateFrom) {
                                $stringPrevTime = $prevTime->toDateTimeString();
                                $pageStatistic[$stringPrevTime] = [
                                    'code' => $report->http_code,
                                    'message' => $report->message
                                ];
                            }
                        }
                    }
                } else {
                    $pageStatistic[$report->last_monitoring_time] = [
                        'code' => $report->http_code,
                        'message' => $report->message
                    ];
                }
            }
        );
        return collect($pageStatistic)->sortKeysDesc();
    }

    public function getPsiChartDataset($page, $dateTo = '', $dateFrom = ''): \Illuminate\Support\Collection
    {
        $reportsQuery = PerformanceMonitoringReport::where('site_id', $this->id)
            ->where('path', $page);
        if ($dateTo && $dateFrom) {
            $reportsQuery = $reportsQuery
                ->where('monitoring_time', '<=', $dateTo)
                ->where('monitoring_time', '>=', $dateFrom);
        }

        $psiStatistic = $reportsQuery->lazy();
        $psiDataArray = [];
        $psiStatistic->each(
            function ($report) use (&$psiDataArray) {
                if (!key_exists($report->monitoring_time, $psiDataArray)) {
                    $psiDataArray[$report->monitoring_time] = [
                        'desktop' => 0,
                        'mobile' => 0,
                    ];
                }
                if ($report->strategy == 0) {
                    $psiDataArray[$report->monitoring_time]['desktop'] = array_merge(
                        ['total_score' => $report->total_score],
                        $report->unpackPSIData()
                    );
                } else {
                    $psiDataArray[$report->monitoring_time]['mobile'] = array_merge(
                        ['total_score' => $report->total_score],
                        $report->unpackPSIData()
                    );
                }
            }
        );
        return collect($psiDataArray)->sortKeysDesc();
    }


    public function getFullUrl($path): string
    {
        return preg_replace('/([^:])\/\//', '$1/', $this->domain . $path);
    }

}
