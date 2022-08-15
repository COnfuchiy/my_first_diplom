<?php


namespace App\Monitoring;


use SimpleXMLElement;
use Exception;

class SitemapParser
{

    private string $error = '';

    public function __construct(
        private string $sitemapUrl = '',
    ) {
    }

    public function initParse(): bool|array
    {
        try {
            $xmlUrls = $this->getXmlSitemap();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
        if ($xmlUrls) {
            $sitemapUrls = [];
            foreach ($xmlUrls as $urlTag) {
                if (isset($urlTag->loc)) {
                    $url = $urlTag->loc;
                    $sitemapUrls[] = ($url);
                }
            }
            return $sitemapUrls;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    private function getXmlSitemap(): SimpleXMLElement|bool
    {
        $sitemap = file_get_contents($this->sitemapUrl);
        if ($sitemap) {
            $xmlSitemap = new SimpleXMLElement($sitemap);
            if ($xmlSitemap) {
                return $xmlSitemap;
            }
        }
        $this->error = error_get_last()['message'];
        return false;
    }

    public function getError(): string{
        return $this->error;
    }

}
