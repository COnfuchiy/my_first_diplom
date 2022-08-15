<?php

namespace App\Models;

use App\Monitoring\HtmlMetaData;
use App\Monitoring\PSIMonitoringData;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Class PerformanceMonitoringReport
 * @property int site_id
 * @property string path
 * @property DateTime|string monitoring_time
 * @property bool strategy
 * @property float total_score
 * @property float FCP
 * @property float TTI
 * @property float speed_index
 * @property float TBT
 * @property float LCP
 * @property float CLS
 * @property string|null title
 * @property string|null description
 * @property string|null h1
 * @package App\Models
 */
class PerformanceMonitoringReport extends Model implements DataSortable
{

    public const MOBILE_STRATEGY = 1;
    public const DESKTOP_STRATEGY = 0;
    public $timestamps = false;
    protected $fillable = [
        'site_id',
        'path',
        'monitoring_time',
    ];

    /**
     * @param $userId
     * @return Builder
     */
    public static function getAllUserReports($userId)
    {

        $userSites = User::find($userId)->sites;
        if($userSites){
            $userSites = $userSites->toQuery()->get('id')->toArray();
            $sitesIdArray = array_column($userSites, 'id');
            return self::whereIn('site_id', $sitesIdArray);
        }
        // TODO ?
        return self::whereIn('site_id', -1);
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

    /**
     * @param Builder $query
     * @param array $searchRequest
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
                    ->select('performance_monitoring_reports.*', 'monitoring_sites.domain');
            }
            $searchRequestParts = parse_url($searchRequest['searchRequest']);
            if (isset($searchRequestParts['host'])) {
                $query = $query->where('domain', 'like', '%' . $searchRequestParts['host'] . '%');
                if (isset($searchRequestParts['path']) && $searchRequestParts['path'] !== '/') {
                    $query = $query
                        ->orWhere('path', 'like', '%' . $searchRequestParts['path'] . '%');
                }
            }
            $query = $query->where('domain', 'like', '%'.$searchRequest['searchRequest'].'%')
                ->orWhere('path', 'like', '%'.$searchRequest['searchRequest'].'%');
        }

        if (isset($searchRequest['dateFrom']) && isset($searchRequest['dateTo'])) {
            $query = $query
                ->where(
                    [
                        ['monitoring_time', '>=', $searchRequest['dateFrom']],
                        ['monitoring_time', '<=', $searchRequest['dateTo']]
                    ]
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
                'sortColumn' => 'in:monitoring_date,domain',
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
                ->select('performance_monitoring_reports.*', 'monitoring_sites.domain');
        }
        if ($sortColumn === 'monitoring_date') {
            return $query->orderBy('monitoring_time', $sortType);
        } else {
            return $query
                ->orderBy('domain', $sortType)
                ->orderBy('path', $sortType);
        }
    }

    public function setupFromPSIData(PSIMonitoringData $PSIData)
    {
        $this->total_score = $PSIData->totalScore;
        $this->FCP = $PSIData->FCP;
        $this->TTI = $PSIData->TTI;
        $this->speed_index = $PSIData->speedIndex;
        $this->TBT = $PSIData->TBT;
        $this->LCP = $PSIData->LCP;
        $this->CLS = $PSIData->CLS;
        $this->strategy = $PSIData->strategy;
    }

    public function setupFromMetaData($metaData)
    {
        $this->title = $metaData['metaTitle'];
        $this->description = $metaData['metaDescription'];
        $this->h1 = $metaData['metaH1'];
    }

    public function site()
    {
        return $this->belongsTo(MonitoringSite::class, 'site_id');
    }

    public function getViewStrategy(): string
    {
        return $this->strategy ? __('monitoring.mobile_strategy') : __('monitoring.desktop_strategy');
    }

    public function unpackPSIData(): array
    {
        $outputData = [];
        $outputData['FCP'] = $this->FCP;
        $outputData['TTI'] = $this->TTI;
        $outputData['speedIndex'] = $this->speed_index;
        $outputData['TBT'] = $this->TBT;
        $outputData['LCP'] = $this->LCP;
        $outputData['CLS'] = $this->CLS;
        return $outputData;
    }

}
