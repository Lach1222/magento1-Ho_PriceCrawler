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
     * Import logs of given job
     *
     * @param string $jobId
     * @param string $spider
     * @return string
     */
    public function importJob($jobId, $spider)
    {
        $result = $this->_importLog($jobId, $spider);

        return $result;
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
            return Mage::helper('ho_pricecrawler')->__('%s not imported: Log already imported', $jobId);
        }

        $log = $this->_getLog($jobId);

        $siteId = Mage::getModel('ho_pricecrawler/sites')
            ->getCollection()
            ->addFieldToFilter('identifier', $spiderName)
            ->getFirstItem()
            ->getSiteId();

        if (!$siteId) {
            // Skip if spider is not yet configured in Magento
            return Mage::helper('ho_pricecrawler')->__('%s not imported: Not configured in Magento', $jobId);
        }

        $importDate = date('Y-m-d H:i:s');

        $resource = Mage::getSingleton('core/resource');
        /** @var Magento_Db_Adapter_Pdo_Mysql $connection */
        $connection = $resource->getConnection('core_write');

        $imported = $errors = 0;

        $lastPageUrlsThreshold = 50;
        $lastPageUrls = array();

        // Import log lines
        foreach ($log as $line) {
            $logDate = date('Y-m-d H:i:s', $line->time / 1000);

            $url = false;
            // Check if log line is a crawled page
            if ($this->_isPage($line) || $this->_isItem($line)) {
                preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $line->message, $match);
                $url = $match[0][0];
            }

            try {
                if ($this->_isPage($line)) {
                    // Log log line as page (no item)
                    $item = array(
                        'site_id' => $siteId,
                        'job_id' => $jobId,
                        'imported_at' => $importDate,
                        'date' => $logDate,
                        'level' => $line->level,
                        'message' => $line->message,
                        'url' => $url,
                        'is_item' => false,
                    );

                    $connection->insert($resource->getTableName('ho_pricecrawler/logs'), $item);
                    $lastInsertId = $connection->lastInsertId();

                    // Remember last log lines to check for items
                    if (count($lastPageUrls) > $lastPageUrlsThreshold) {
                        $lastPageUrls = array_slice($lastPageUrls, 1, null, true);
                    }
                    $lastPageUrls[$lastInsertId] = $url;

                    $imported++;
                }
                elseif ($this->_isItem($line)) {
                    if (in_array($url, $lastPageUrls)) {
                        // Changed already saved log item, set is_item to true
                        $logLineId = array_search($url, $lastPageUrls);
                        $connection->update($resource->getTableName('ho_pricecrawler/logs'), array('is_item' => 1), array('entity_id = ?' => $logLineId));
                    }
                }
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

}