<?php

class Ho_PriceCrawler_Model_Scrapinghub_Logs extends Ho_PriceCrawler_Model_Scrapinghub_Abstract
{
    const IMPORT_JOB_LIMIT = 20;

    /**
     * Import logs of finished spiders
     *
     * @return array
     */
    public function import()
    {
        $jobsModel = Mage::getModel('ho_pricecrawler/scrapinghub_jobs');
        $jobs = $jobsModel->listJobs(array('finished'));

        $messages = array();
        $i = 0;
        foreach ($jobs as $job) {
            $jobId = $job->id;
            $spider = $job->spider;

            $messages[] = $this->_importLog($jobId, $spider);

            $i++;
            if ($i == self::IMPORT_JOB_LIMIT) break;
        }

        return $messages;
    }

    /**
     * Import log for given jobId with spiderName
     *
     * @param string $jobId
     * @param string $spiderName
     * @return string
     */
    protected function _importLog($jobId, $spiderName)
    {
        $logModel = Mage::getModel('ho_pricecrawler/logs')
            ->getCollection()
            ->addFieldToFilter('job_id', $jobId)
            ->getFirstItem();

        if ($logModel->getId()) {
            // Skip if log is already imported
            return Mage::helper('ho_pricecrawler')->__('Skip job %s: Log already imported', $jobId);
        }

        $log = $this->_getLog($jobId);

        $siteId = Mage::getModel('ho_pricecrawler/sites')
            ->getCollection()
            ->addFieldToFilter('identifier', $spiderName)
            ->getFirstItem()
            ->getSiteId();

        if (!$siteId) {
            // Skip if spider is not yet configured in Magento
            return Mage::helper('ho_pricecrawler')->__('Skip job %s: Not configured in Magento', $jobId);
        }

        $importDate = date('Y-m-d H:i:s');

        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_write');

        $imported = $errors = 0;
        $lastItem = $logItem = array();
        $lastLine = $lastUrl = false;

        // Import log lines
        foreach ($log as $line) {
            $logDate = date('Y-m-d H:i:s', $line->time / 1000);

            $item = $url = $isItem = false;
            // Check if log line is a crawled page
            if ($this->_isPage($line) || $this->_isItem($line)) {
                preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $line->message, $match);
                $url = $match[0][0];
            }

            $item = array(
                'site_id'       => $siteId,
                'job_id'        => $jobId,
                'imported_at'   => $importDate,
                'date'          => $logDate,
                'level'         => $line->level,
                'message'       => $line->message,
                'url'           => $url,
                'is_item'        => false,
            );

            if ($this->_isItem($lastLine)) {
                // Last line was item, log it
                $lastItem['is_item'] = true;
                $logItem = $lastItem;
            }
            elseif ($this->_isPage($lastLine)) {
                // Last line was page request
                if ($this->_isItem($line)) {
                    // Don't log last line, because this one is item (else it will be double in the logs)
                    $logItem = array();
                }
                else {
                    // Log it
                    $logItem = $lastItem;
                }
            }
            else {
                // Last line was not item or request
                $logItem = array();
            }

            $lastLine = $line;
            $lastItem = $item;

            // Check if we need to save log line
            if (empty($logItem)) continue;

            try {
                $connection->insert($resource->getTableName('ho_pricecrawler/logs'), $logItem);
                $imported++;
            }
            catch (Exception $e) {
                Mage::logException($e);
                $errors++;
            }
        }

        return Mage::helper('ho_pricecrawler')->__('Imported log lines for %s: %s (%s errors)', $jobId, $imported, $errors);
    }

    /**
     * Get log of given job ID via API
     *
     * @param string $jobId
     * @return array
     */
    protected function _getLog($jobId)
    {
        $result = $this->get('log', array('job' => $jobId));

        return json_decode($result);
    }

    /**
     * @param $logLine
     * @return bool
     */
    protected function _isPage($logLine)
    {
        if (!is_object($logLine)) return false;

        return strpos($logLine->message, 'Crawled (') === 0;
    }

    /**
     * @param $logLine
     * @return bool
     */
    protected function _isItem($logLine)
    {
        if (!is_object($logLine)) return false;

        return strpos($logLine->message, 'Scraped from') === 0;
    }

    // todo: clean old logs
}