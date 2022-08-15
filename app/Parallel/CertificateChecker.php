<?php


namespace App\Parallel;


use DateTime;
use DateTimeZone;
use Exception;

class CertificateChecker
{
    protected string $url;
    protected array $result;
    protected string $dateFormat;
    protected string $formatString;
    protected ?string $timeZone;
    protected float $timeOut;

    /**
     * CertificateChecker constructor.
     * @param string $url
     * @param string $dateFormat
     * @param string $formatString
     * @param string|null $timeZone
     * @param float $timeOut
     * @throws Exception
     */
    public function __construct(
        string $url,
        string $dateFormat = 'U',
        string $formatString = 'd-m-Y H:i:s',
        ?string $timeZone = null,
        float $timeOut = 30
    ) {
        if (is_null($url)) {
            throw new Exception('Checked url is empty');
        }
        if (!$this->isValidUrl($url)) {
            throw new Exception('Invalid url');
        }
        $parsedUrl = parse_url($url,PHP_URL_HOST);
        if(!$parsedUrl){
            throw new Exception('Invalid url');
        }
        $this->url = $parsedUrl;
        $this->dateFormat = $dateFormat;
        $this->timeZone = $timeZone;
        $this->formatString = $formatString;
        $this->timeOut = $timeOut;
        $this->result = [];
    }

    /**
     * @param string $data
     * @return bool
     */
    private function isValidUrl(string $data): bool
    {
        $regex =
            "%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*" .
            "[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*" .
            "(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu";

        return (1 === preg_match($regex, $data));
    }

    /**
     * @return array
     * @throws Exception
     */
    public function checkSsl(): array
    {
        $cert =
            stream_socket_client(
                'ssl://' . $this->url . ':443',
                $errno,
                $messageError,
                $this->timeOut,
                STREAM_CLIENT_CONNECT,
                $this->getStreamContext()
            );

        if ($cert === false) {
            throw new Exception();
        }

        return $this->getSLLInformation($cert);
    }

    /**
     * @return resource
     */
    private function getStreamContext()
    {
        return stream_context_create(
            [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'capture_peer_cert' => true
                ]
            ]
        );
    }

    /**
     * @param resource $siteStream
     * @return array
     * @throws Exception
     */
    private function getSLLInformation($siteStream): array
    {
        try {
            if (!is_resource($siteStream) || get_resource_type($siteStream) !== 'stream') {
                throw new Exception('Parameter $siteStream not type stream');
            }
            $certStream = stream_context_get_params($siteStream);
            $cert = $this->getCertFromArray($certStream);
            $certInfo = openssl_x509_parse($cert);
            $isValid = time() <= $certInfo['validTo_time_t'];
            $valid_from = $this->normalizeDate((string)$certInfo['validFrom_time_t']);
            $valid_to = $this->normalizeDate((string)$certInfo['validTo_time_t']);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return [
            'is_valid' => $isValid,
            'created_at' => $valid_from,
            'valid_until' => $valid_to
        ];
    }

    /**
     * @param array $certStream
     * @return resource
     */
    private function getCertFromArray(array $certStream)
    {
        return $certStream['options']['ssl']['peer_certificate'];
    }

    /**
     * @param string $timeStamp
     * @return string|bool
     */
    private function normalizeDate(string $timeStamp): bool|string
    {
        $timeZone = null;
        if ($this->timeZone !== null) {
            $timeZone = new DateTimeZone($this->timeZone);
        }
        return DateTime::createFromFormat($this->dateFormat, $timeStamp, $timeZone)->format($this->formatString);
    }
}
