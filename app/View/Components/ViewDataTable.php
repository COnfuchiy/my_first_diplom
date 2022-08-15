<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ViewDataTable extends Component
{

    private static array $tablesTypes = [
        'sites' => 'components.data-tables.view-sites-table',
        'availabilityReports' => 'components.data-tables.view-availability-reports-table',
        'performanceReports' => 'components.data-tables.view-performance-reports-table',
        'overview' => 'components.data-tables.view-overview-table'
    ];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $tableType,
        public int $currentPage = 1,
        public iterable $rowsData = [],
        public int $totalPageCount = 1,
    ) {
        if (!key_exists($tableType, self::$tablesTypes)) {
            // TODO log error
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view(
            self::$tablesTypes[$this->tableType],
            ['rowsData' => $this->rowsData, 'totalPageCount' => $this->totalPageCount, 'currentPage'=>$this->currentPage]
        );
    }
}
