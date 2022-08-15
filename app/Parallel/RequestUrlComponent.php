<?php

namespace App\Parallel;


use CurlHandle;
use Exception;
use JetBrains\PhpStorm\ArrayShape;


class RequestUrlComponent
{
    private CurlHandle $curl;

    private int $returnCode = 0;

    private string $errorMessage = '';

    public array $metaData;


    /**
     * @throws Exception
     */
    public function __construct(
        private string $url,
        private int $timeout,
        private bool $parseMeta = false
    ) {
        $this->curlInit();
    }

    public function getErrorMessage():string
    {
        return $this->errorMessage;
    }

    public function getCode():int
    {
        return $this->returnCode;
    }


    public function requestToUrl(): bool
    {
        $result = curl_exec($this->curl);
        if ($result === false) {
            if ($errorCode = curl_errno($this->curl)) {
                $infoUrl = curl_getinfo($this->curl);
                $this->returnCode = $infoUrl['http_code'];
                $errorMessage = curl_strerror($errorCode);
                if ($errorMessage !== null) {
                    $this->errorMessage = $errorMessage;
                }
            }
            curl_close($this->curl);
            return false;
        } else {
            $infoUrl = curl_getinfo($this->curl);
            if($this->parseMeta){
                $this->parseMetaTags($result);
            }
            $this->returnCode = $infoUrl['http_code'];
        }
        curl_close($this->curl);
        return true;
    }


    private function curlInit()
    {
        $this->curl = curl_init($this->url);
        curl_setopt_array($this->curl, $this->setupCurlConfig());
    }


    #[ArrayShape([
        CURLOPT_FAILONERROR => "bool",
        CURLOPT_FRESH_CONNECT => "bool",
        CURLOPT_HEADER => "bool",
        CURLOPT_RETURNTRANSFER => "bool",
        CURLOPT_FOLLOWLOCATION => "int",
        CURLOPT_MAXREDIRS => "int",
        CURLOPT_CONNECTTIMEOUT => "int",
        CURLOPT_TIMEOUT => "int"
    ])] private function setupCurlConfig(): array
    {
        $curlConfigArray = [
            CURLOPT_FAILONERROR => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 5
        ];
        if(isset($this->timeout) && $this->timeout){
            $curlConfigArray[CURLOPT_TIMEOUT] = $this->timeout;
        }
        return $curlConfigArray;
    }

    private function parseMetaTags(string $rawHtml): void {
        $metaTags = [
            'metaTitle'=>'',
            'metaDescription'=>'',
            'metaH1'=>''
        ];
        preg_match('/<title>(.+)<\/title>/u',$rawHtml,$outputArray);
        if(isset($outputArray[0])){
            $metaTags['metaTitle'] = $outputArray[1];
        }
        preg_match('/<meta name="description" content="(.+)"\s?\/?>/u',$rawHtml,$outputArray);
        if(isset($outputArray[0])){
            $metaTags['metaDescription'] = $outputArray[1];

        }
        preg_match('/<h1>(.+)<\/h1>/u',$rawHtml,$outputArray);
        if(isset($outputArray[0])){
            $metaTags['metaH1'] = $outputArray[1];
        }

        $this->metaData = $metaTags;
    }
}
