<?php

/**
 * Class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Jobs
 *
 * @method string getName()
 * @method array getJobs()
 */
class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Jobs extends Mage_Adminhtml_Block_Dashboard_Grid
{
    protected $_loggedJobIds;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ho/pricecrawler/jobs.phtml');
    }

    /**
     * Retrieve Scrapinghub job URL
     *
     * @param string $jobId
     * @return string
     */
    public function getJobUrl($jobId)
    {
        return Mage::helper('ho_pricecrawler')->getScrapinghubJobUrl($jobId);
    }

    /**
     * Check if given job ID has logs
     *
     * @param int $jobId
     * @return bool
     */
    public function showLogUrl($jobId)
    {
        $jobs = $this->_getLoggedJobIds();

        return in_array($jobId, $jobs);
    }

    /**
     * Retrieve all jobs IDs that have logs
     *
     * @return array
     */
    protected function _getLoggedJobIds()
    {
        if (is_null($this->_loggedJobIds)) {
            $resource = Mage::getModel('core/resource');
            $connection = $resource->getConnection('core_read');

            $sql = $connection->select()
                ->from($resource->getTableName('ho_pricecrawler/logs'))
                ->reset(Zend_Db_Select::COLUMNS)
                ->distinct()
                ->columns('job_id');

            $result = array_keys($connection->fetchPairs($sql));

            $this->_loggedJobIds = $result;
        }

        return $this->_loggedJobIds;
    }
}