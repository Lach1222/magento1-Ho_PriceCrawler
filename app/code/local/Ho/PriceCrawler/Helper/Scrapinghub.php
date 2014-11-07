<?php

class Ho_PriceCrawler_Helper_Scrapinghub
{
    const SH_BASE_URL = 'https://dash.scrapinghub.com';

    /**
     * Retrieve Scrapinghub job URL by given job id (<project>/<spider>/<jobID>).
     *
     * For example, when giving argument '1234/56/7':
     * https://dash.scrapinghub.com/p/1234/job/56/7/
     *
     * @param string $jobId
     * @return string
     */
    public function getJobUrl($jobId)
    {
        $job = explode('/', $jobId);
        list($project, $spider, $jobId) = $job;

        $url = sprintf(self::SH_BASE_URL . '/p/%s/job/%s/%s', $project, $spider, $jobId);

        return $url;
    }

    /**
     * Retrieve Scrapinghub requests-search url
     *
     * Example:
     * https://dash.scrapinghub.com/p/<project>/job/<spider>/<job>/#requests/filter/url/is_exact/<url>
     *
     * @param string $jobId
     * @param string $url
     */
    public function getUrlSearchUrl($jobId, $url)
    {
        $jobUrl = $this->getJobUrl($jobId);

        $searchUrl = $jobUrl . '/#requests/filter/url/is_exact/' . urlencode($url);

        return $searchUrl;
    }
}