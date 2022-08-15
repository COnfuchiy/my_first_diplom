<?php


namespace App\Parallel;

use App\Models\AvailabilityMonitoringReport;
use App\Monitoring\TelegramComponent;
use mysqli;

class AvailabilityMonitoringReportComponent
{

    private mysqli|false|null $db;

    public function __construct(
        private int $telegramId
    )
    {
        $this->db = mysqli_connect(
            getenv('DB_HOST') . ':' . getenv('DB_PORT'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            getenv('DB_DATABASE')
        );
        if (!$this->db) {
            //
            var_dump(mysqli_connect_error());

        }
        mysqli_set_charset($this->db, "utf8");
        mysqli_options($this->db, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
    }

    public static function checkReportsForClean($site)
    {
        $cleanReportPeriod = $site->availability_report_clear_num_days;
        $reports = AvailabilityMonitoringReport::where(
            'last_monitoring_time',
            '>',
            now()->addDays($cleanReportPeriod)
        )->where('user_id', $site->user_id);
        if ($reports->count()) {
            $reports->delete();
        }
    }


    public function processMonitoringResults(
        int $siteId,
        string $url,
        int $code,
        string $datetime,
        string $message = ''
    ): bool {
        $this->prevReport = [];
        $safeSiteId = $this->db->real_escape_string($siteId);
        $pagePath = $this->db->real_escape_string(parse_url($url, PHP_URL_PATH));
        $lastReport = mysqli_query(
            $this->db,
            "select id,http_code,monitoring_sequence from availability_monitoring_reports where path = '$pagePath' and site_id = $safeSiteId order by last_monitoring_time desc LIMIT 1"
        );

        $safeDatetime = $this->db->real_escape_string($datetime);
        if (!$lastReport || mysqli_num_rows($lastReport) === 0 || ($lastReport = mysqli_fetch_array(
                $lastReport,
                MYSQLI_ASSOC
            ))['http_code'] !== $code) {
            if (is_array($lastReport)) {
                if ($lastReport['http_code'] < 400 && $code >= 400 || $lastReport['http_code'] >= 400 && $code >= 400) {
                    TelegramComponent::monitoringNotify(
                        $this->telegramId,
                        sprintf('%s недоступен, код ошибки %d - %s, код предыдущего состояния %n',
                            $url, $code, $message, $lastReport['http_code'],
                        )
                    );
                } else {
                    TelegramComponent::monitoringNotify(
                        $this->telegramId,
                        sprintf('%s снова доступен(%d), код предыдущего состояния %n',
                               $url, $code, $lastReport['http_code'],
                        )
                    );
                }
            } elseif ($code >= 400) {
                TelegramComponent::monitoringNotify(
                    $this->telegramId,
                    sprintf('%s недоступен, код ошибки %d - %s',
                           $url, $code, $message,
                    )
                );
            } else {
//                TelegramComponent::monitoringNotify(
//                    $this->telegramId,
//                    __('telegram.new_success_report',[
//                        'url'=>$url,
//                        'code'=>$code,
//                    ])
//                );
            }
            $safeMessage = $this->db->real_escape_string($message);
            return mysqli_query(
                $this->db,
                "insert into availability_monitoring_reports(`site_id`,`path`,`first_monitoring_time`,`http_code`,`message`,`monitoring_sequence`,`last_monitoring_time`) values  ('$siteId', '$pagePath', '$datetime', '$code','$safeMessage', 1,'$datetime');"
            );
        } else {
            $reportId = $lastReport['id'];
            $newMonitoringSequence = (integer)$lastReport['monitoring_sequence'] + 1;
            return mysqli_query(
                $this->db,
                "update availability_monitoring_reports set last_monitoring_time = '$safeDatetime', monitoring_sequence = $newMonitoringSequence where id = $reportId;"
            );
        }
    }

}
