<?php

class Ho_PriceCrawler_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_FINISHED_JOBS_LIMIT = 'ho_pricecrawler/dashboard/finished_job_limit';
    const XML_PATH_IMPORT_LOG_LIMIT = 'ho_pricecrawler/dashboard/import_log_limit';

    protected $_loggedJobIds;

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
     * Get URL with filter params (job ID set, and is_item set to 'No')
     *
     * @param int $jobId
     * @return string
     */
    public function getLogsGridUrl($jobId)
    {
        $filters = array(
            'job_id'    => $jobId,
            'is_item'   => 0,
        );

        $filter = '';
        $i = 0;
        foreach ($filters as $key => $value) {
            $filter .= ($i > 0 ? '&' : '') . $key . '=' . $value;
            $i++;
        }

        $url = Mage::helper('adminhtml')->getUrl('ho_pricecrawler/adminhtml_logs', array('limit' => 200, 'filter' => base64_encode($filter)));

        return $url;
    }

    /**
     * Retrieve all jobs IDs that have logs
     *
     * @return array
     */
    public function getLoggedJobIds()
    {
        if (is_null($this->_loggedJobIds)) {
            $resource = Mage::getModel('core/resource');
            $connection = $resource->getConnection('core_read');

            $sql = $connection->select()
                ->from($resource->getTableName('ho_pricecrawler/logs'))
                ->reset(Zend_Db_Select::COLUMNS)
                ->distinct()
                ->columns('job_id');

            $result = $connection->fetchAll($sql);

            $jobs = array();
            foreach ($result as $job) {
                $jobs[] = $job['job_id'];
            }

            $this->_loggedJobIds = $jobs;
        }

        return $this->_loggedJobIds;
    }

}