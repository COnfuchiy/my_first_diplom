<?php


namespace App\Monitoring;

use App\Monitoring\PSIMonitoringData;
use Exception;

class PageSpeedInsightsComponent
{
    public const API = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    private static array $resolvedMetrics = [
        'first-contentful-paint',
        'interactive',
        'speed-index',
        'total-blocking-time',
        'largest-contentful-paint',
        'cumulative-layout-shift'
    ];

    private static array $metricsWeights = [
        "FCP" => 10,
        "TTI" => 10,
        "speedIndex" => 10,
        "TBT" => 30,
        "LCP" => 25,
        "CLS" => 15,
    ];

    public function __construct(
        private string $absolutUrl,
        private int $requestStrategy = 0,
        private string $apiKey = ''
    ) {
    }

    /**
     * @throws Exception
     */
    public function analyseUrl(): PSIMonitoringData
    {
        $response = file_get_contents($this->constructApiRequest());

        if ($response !== false && $response) {
            return new PSIMonitoringData($this->parsePageSpeedData($response), $this->requestStrategy);
        }
        throw new Exception('Request error: ' . error_get_last()['message']);
    }

    private function constructApiRequest(): string
    {
        $outputStrategy = $this->getOutputStrategy();
        return self::API . "?url=$this->absolutUrl&strategy=$outputStrategy" . ($this->apiKey ? "&key=" . $this->apiKey : '');
    }


    private function getOutputStrategy(): string
    {
        return $this->requestStrategy?'mobile':'desktop';
    }

    private function calculateTotalScore(array $scoreArray): float {
        $totalScore = 0;
        foreach ($scoreArray as $metric => $score){
            $totalScore+=$score*self::$metricsWeights[$metric];
        }
        return round($totalScore);
    }

    private function parsePageSpeedData(string $pageSpeedData): array
    {
        $data = json_decode($pageSpeedData, true);
        if (isset($data['lighthouseResult']) && isset($data['lighthouseResult']['audits'])) {
            $audits = $data['lighthouseResult']['audits'];
            // check if each metric value and score exists
            foreach (self::$resolvedMetrics as $metric) {
                if (!isset($audits[$metric])) {
                    if (!isset($audits[$metric]['displayValue'])) {
                        $audits[$metric]['displayValue'] = -1;
                    }
                    if (!isset($audits[$metric]['score'])) {
                        $audits[$metric]['score'] = 0;
                    }
                }
            }
            $scoreArray = [];
            $resultArray = [];
            $auditsIndex = 0;

            // use only keys as metrics names
            foreach (array_keys(self::$metricsWeights) as $metricsName) {
                $resultArray[$metricsName] = (float)$audits[self::$resolvedMetrics[$auditsIndex]]['displayValue'];
                $scoreArray[$metricsName] = (float)$audits[self::$resolvedMetrics[$auditsIndex]]['score'];
                $auditsIndex++;
            }

            $resultArray['total-score'] = $this->calculateTotalScore($scoreArray);

            return $resultArray;
        }
        return [];
    }

}
