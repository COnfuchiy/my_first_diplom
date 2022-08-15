<?php


namespace App\Monitoring;


class PSIMonitoringData
{

    public float $FCP;
    public float $TTI;
    public float $speedIndex;
    public float $TBT;
    public float $LCP;
    public float $CLS;
    public float $totalScore;
    public bool $strategy;

    public function __construct(array $pageSpeedData,bool $strategy)
    {
        $this->FCP = (float)$pageSpeedData['FCP'] ?? .0;
        $this->TTI = (float)$pageSpeedData['TTI'] ?? .0;
        $this->speedIndex = (float)$pageSpeedData['speedIndex'] ?? .0;
        $this->TBT = (float)$pageSpeedData['TBT'] ?? .0;
        $this->LCP = (float)$pageSpeedData['LCP'] ?? .0;
        $this->CLS = (float)$pageSpeedData['CLS'] ?? .0;
        $this->totalScore = (float)$pageSpeedData['total-score'] ?? .0;
        $this->strategy = $strategy;
    }

}
