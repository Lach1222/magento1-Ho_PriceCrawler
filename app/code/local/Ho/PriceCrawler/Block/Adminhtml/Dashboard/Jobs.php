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
     * Get URL with filter params (job ID set, and is_item set to 'No')
     *
     * @param int $jobId
     * @return string
     */
    public function getLogsGridUrl($jobId)
    {
        $helper = Mage::helper('ho_pricecrawler');

        $url = $helper->getLogsGridUrl($jobId);

        return $url;
    }

    /**
     * Retrieve Scrapinghub job URL
     *
     * @param string $jobId
     * @return string
     */
    public function getJobUrl($jobId)
    {
        return Mage::helper('ho_pricecrawler/scrapinghub')->getJobUrl($jobId);
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

    protected function _getLoggedJobIds()
    {
        if (is_null($this->_loggedJobIds)) {
            $jobs = Mage::helper('ho_pricecrawler')->getLoggedJobIds();

            $this->_loggedJobIds = $jobs;
        }

        return $this->_loggedJobIds;
    }
}