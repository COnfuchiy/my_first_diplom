<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait DataTableModel
{

    public static array $tablesViews = [
        'sites' => [
            'index' => 'sites-list.index',
            'table' => 'components.data-tables.sites-table'
        ],
        'availability-reports' => [
            'index' => 'availability-reports.index',
            'table' => 'components.data-tables.availability-reports-table'
        ],
        'performance-reports' => [
            'index' => 'performance-reports.index',
            'table' => 'components.data-tables.performance-reports-table'
        ],
    ];

    protected function processingDataTable(
        string $tableType,
        Request $request,
        Builder $baseQuery,
        string $modelClassName,
        string $defaultSortColumn,
        string $defaultSortType,
        string $searchMethod = 'search',
        string $filterMethod = 'filter',
        string $validateFilterParamsMethod = 'validateFilterParams',
        string $validateSearchParamsMethod = 'validateSearchParams',
        int $pageSize = 10,
    ) {
        if (!key_exists($tableType, self::$tablesViews)) {
            // TODO log error
        }

        if (isset($request->searchRequest) || isset($request->dateFrom)) {
            if (call_user_func(array($modelClassName, $validateSearchParamsMethod), $request->all())) {
                throw new HttpException(500, 'Parameters are not valid!');
            }
            $searchParams = [];
            if (isset($request->searchRequest)) {
                $searchParams = [
                    'searchRequest' => $request->searchRequest
                ];
            }
            if (isset($request->dateFrom) && isset($request->dateTo)) {
                $searchParams = array_merge(
                    $searchParams,
                    [
                        'dateFrom' => $request->dateFrom,
                        'dateTo' => $request->dateTo
                    ]
                );
            }
            $baseQuery = call_user_func(array($modelClassName, $searchMethod), $baseQuery, $searchParams);
        }

        $totalObjectsCount = $baseQuery->count();
        $totalObjectsPagesCount = (int)ceil($totalObjectsCount / $pageSize);

//        // if request is ajax and set sort column
//        if ($request->ajax() && isset($request->sortColumn)) {
//            if (!call_user_func(array($modelClassName, $validateFilterParamsMethod), $request->all())) {
//                throw new HttpException(500, 'Incorrect sort column!');
//            }
//            $sortedQuery = call_user_func(
//                array($modelClassName, $filterMethod),
//                $baseQuery,
//                $request->sortColumn,
//                $request->sortType
//            );
//            // Get output data given the current page
//            $outputData = $sortedQuery->simplePaginate($pageSize);
//            // Render data table
//            return view(
//                self::$tablesViews[$tableType]['table'],
//                [
//                    'rowsData' => $outputData,
//                    'totalPageCount' => $totalObjectsPagesCount
//                ]
//            );
//        }
        // if request is ajax and set sort column
        if (isset($request->sortColumn)) {
            if (call_user_func(array($modelClassName, $validateFilterParamsMethod), $request->all())) {
                throw new HttpException(501, 'Incorrect sort column!');
            }
            $sortedQuery = call_user_func(
                array($modelClassName, $filterMethod),
                $baseQuery,
                $request->sortColumn,
                $request->sortType
            );
        }
        else{
            $sortedQuery = call_user_func(
                array($modelClassName, $filterMethod),
                $baseQuery,
                $defaultSortColumn,
                $defaultSortType
            );
        }
        // Get output data given the current page
        $outputData = $sortedQuery->simplePaginate($pageSize);
        $view = self::$tablesViews[$tableType][$request->ajax() ? 'table' : 'index'];
        // Render data table
        return view(
            $view,
            [
                'rowsData' => $outputData,
                'totalPageCount' => $totalObjectsPagesCount,
                'currentPage'=>isset($_GET['page'])?(integer)$_GET['page']:1
            ]
        );
    }

}
