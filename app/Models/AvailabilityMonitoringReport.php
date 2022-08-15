<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\LazyCollection;


/**
 * Class AvailabilityMonitoringReport
 * @param int site_id
 * @param string path
 * @param \DateTime first_monitoring_time
 * @param int http_code
 * @param string message
 * @param int monitoring_sequence
 * @param \DateTime last_monitoring_time
 * @package App\Models
 */
class AvailabilityMonitoringReport extends Model implements DataSortable
{
    use HasFactory;

    public const ALL_PAGES = 1;

    public const SPECIFY_PAGES = 2;

    public const SORT_BY_LAST_DATE = 'last-date';

    public const SORT_BY_DOMAIN = 'domain';

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'site_id',
        'path',
        'first_monitoring_time',
        'http_code',
        'message',
        'monitoring_sequence',
        'last_monitoring_time'
    ];

    public static function getAllUserReports($userId)
    {
        $userSites = User::find($userId)->sites->toQuery()->get('id')->toArray();
        $sitesIdArray = array_column($userSites, 'id');
        return self::whereIn('site_id', $sitesIdArray)->get();
    }

    public static function getUrlStatus($url, $siteId): ?bool
    {
        $successReport = self::getSuccessReportOrNull($url, $siteId);
        $errorReport = self::getLastErrorReportOrNull($url, $siteId);
        if ($successReport && $errorReport) {
            if ($successReport->last_monitoring_time > $errorReport->last_monitoring_time) {
                return true;
            }
            return false;
        }
        if ($successReport) {
            return true;
        }
        if ($errorReport) {
            return false;
        }
        return null;
    }

    public static function getSuccessReportOrNull($path, $siteId)
    {
        $successReport = self::where('http_code', '>=', 200)
            ->where('path', $path)
            ->where('site_id', $siteId)
            ->where('http_code', '<', 400)
            ->latest('last_monitoring_time')
            ->first();
        return $successReport ?? null;
    }

    public static function getLastErrorReportOrNull($path, $siteId)
    {
        $errorReport = self::where('http_code', '>=', 400)
            ->where('path', $path)
            ->where('site_id', $siteId)
            ->latest('last_monitoring_time')
            ->first();
        return $errorReport ?? null;
    }

    /**
     * @param Builder $query
     * @param string|array $searchRequest
     * @return Builder
     */
    public static function search(Builder $query, array $searchRequest): Builder
    {
        if (isset($searchRequest['searchRequest'])) {
            $joins = $query->getQuery()->joins;
            if (!$joins) {
                $query = $query
                    ->join(
                        'monitoring_sites',
                        'monitoring_sites.id',
                        '=',
                        'site_id'
                    )
                    ->select('availability_monitoring_reports.*', 'monitoring_sites.domain');
            }
            $searchRequestParts = parse_url($searchRequest['searchRequest']);
            if (isset($searchRequestParts['host'])) {
                $query = $query->where('domain', 'like', '%' . $searchRequestParts['host'] . '%');
                if (isset($searchRequestParts['path']) && $searchRequestParts['path'] !== '/') {
                    return $query
                        ->orWhere('path', 'like', '%' . $searchRequestParts['path'] . '%');
                }
            }
            $query = $query->where('domain', 'like', '%'.$searchRequest['searchRequest'].'%')
                ->orWhere('path', 'like', '%'.$searchRequest['searchRequest'].'%');
        }

        if (isset($searchRequest['dateFrom']) && isset($searchRequest['dateTo'])) {
            $query = $query->where(
                [
                    ['last_monitoring_time', '>=', $searchRequest['dateFrom']],
                    ['last_monitoring_time', '<=', $searchRequest['dateTo']],
                ]
            )
                ->orWhere(
                    function ($query) use ($searchRequest) {
                        $query->where('first_monitoring_time', '<=', $searchRequest['dateFrom'])
                            ->where('last_monitoring_time', '>=', $searchRequest['dateFrom']);
                    }
                )
                ->orWhere(
                    function ($query) use ($searchRequest) {
                        $query->where('first_monitoring_time', '<=', $searchRequest['dateTo'])
                            ->where('last_monitoring_time', '>=', $searchRequest['dateTo']);
                    }
                );
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
                'sortColumn' => 'in:'.self::SORT_BY_LAST_DATE.','.self::SORT_BY_DOMAIN,
                'sortType' => 'in:asc,desc'
            ]
        );
        return $validator->fails();
    }

    /**
     * @param Builder $query
     * @param string $sortColumn
     * @param string $sortType
     * @return Builder
     */
    public static function filter(Builder $query, string $sortColumn, string $sortType): Builder
    {
        $joins = $query->getQuery()->joins;
        if (!$joins) {
            $query = $query
                ->join(
                    'monitoring_sites',
                    'monitoring_sites.id',
                    '=',
                    'site_id'
                )
                ->select('availability_monitoring_reports.*', 'monitoring_sites.domain');
        }
         if ($sortColumn === self::SORT_BY_DOMAIN) {
            return $query
                ->orderBy('domain', $sortType)
                ->orderBy('path', $sortType);
        }
        return $query->orderBy('last_monitoring_time', $sortType);
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
                'dateTo' => 'nullable|date',
                'dateFrom' => 'nullable|date',
            ]
        );
        return $validator->fails();
    }

    public static function getLastReport($site_id)
    {
        $errorReport = self::where('site_id', $site_id)
            ->latest('last_monitoring_time')
            ->first();
        return $errorReport;
    }

    public static function getAllReportsByDate(
        int $site_id,
        Carbon|string $dateTo,
        Carbon|string $dateFrom,
        int $pageType = AvailabilityMonitoringReport::ALL_PAGES,
        string $specialPage = ''
    ): LazyCollection {
        $query = self::where('site_id', $site_id);
        if ($pageType === self::SPECIFY_PAGES) {
            $query = $query->where('path', $specialPage);
        }
        if ($dateTo && $dateFrom) {
            $query = $query->where(
                [
                    ['last_monitoring_time', '>=', $dateFrom],
                    ['last_monitoring_time', '<=', $dateTo],
                ]
            )
                ->orWhere(
                    function ($query) use ($dateFrom) {
                        $query->where('first_monitoring_time', '<=', $dateFrom)
                            ->where('last_monitoring_time', '>=', $dateFrom);
                    }
                )
                ->orWhere(
                    function ($query) use ($dateTo) {
                        $query->where('first_monitoring_time', '<=', $dateTo)
                            ->where('last_monitoring_time', '>=', $dateTo);
                    }
                );
        }
        return $query->lazy();
    }

    public static function getAllReportsByDates(
        int $site_id,
        string $date,
        int $pageType,
    ): LazyCollection {
        $query = self::where('site_id', $site_id)
            ->where('last_monitoring_time', '=', $date)
            ->orWhere(
                function ($query) use ($date) {
                    $query->where('first_monitoring_time', '<', $date)
                        ->where('last_monitoring_time', '>=', $date);
                }
            );
        if ($pageType === 1) {
            $query = $query->where('path', '/');
        }
        if ($pageType === 2) {
            $query = $query->where('path', '!=', '/');
        }
        return $query->lazy();
    }

    public function site()
    {
        return $this->belongsTo(MonitoringSite::class, 'site_id');
    }
}
