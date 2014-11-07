<?php

class Ho_PriceCrawler_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_FINISHED_JOBS_LIMIT = 'ho_pricecrawler/dashboard/finished_job_limit';
    const XML_PATH_IMPORT_LOG_LIMIT = 'ho_pricecrawler/dashboard/import_log_limit';

    public function getFinishedJobLimit()
    {
        return Mage::getStoreConfig(self::XML_PATH_FINISHED_JOBS_LIMIT);
    }

    public function getImportLogLimit()
    {
        return Mage::getStoreConfig(self::XML_PATH_IMPORT_LOG_LIMIT);
    }

    public function parseJobTimestamp($timestamp)
    {
        $timestamp = explode('T', $timestamp);
        $date = Mage::helper('core')->formatDate($timestamp[0], Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $time = Mage::helper('core')->formatTime($timestamp[1], Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        return $date . ' ' . $time;
    }

    public function getElapsedJobTime($job)
    {
        $start = explode('T', $job->started_time);
        $end = explode('T', $job->updated_time);

        $start = new DateTime($start[0] . ' ' . $start[1]);
        $end = new DateTime($end[0] . ' ' . $end[1]);
        $interval = $start->diff($end);

        return
            (($interval->d > 0) ? $interval->d .  'd ' : '') .
            (($interval->d > 0 || $interval->h > 0) ? $interval->h . 'h ' : '') .
            (($interval->d > 0 || $interval->h > 0 || $interval->i > 0) ? $interval->i . 'm ' : '') .
            $interval->s . 's';
    }

    /**
     * Retrieve Scrapinghub URL by given job id (<project>/<spider>/<jobID>).
     *
     * For example, when giving argument '1234/56/7':
     * https://dash.scrapinghub.com/p/1234/job/56/7/
     *
     * @param string $jobId
     * @return string
     */
    public function getScrapinghubJobUrl($jobId)
    {
        $job = explode('/', $jobId);
        list($project, $spider, $jobId) = $job;

        $url = sprintf('https://dash.scrapinghub.com/p/%s/job/%s/%s/', $project, $spider, $jobId);

        return $url;
    }

}