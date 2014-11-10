<?php

class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Schedule extends Mage_Adminhtml_Block_Dashboard_Abstract
{
    protected $_loggedJobIds;

    public function getSpiders()
    {
        return Mage::getModel('ho_pricecrawler/sites')->getActiveSites();
    }

    public function getSpidersWithJobInfo()
    {
        $spiders = $this->getSpiders();

        foreach ($spiders as $spider) {
            $jobs = Mage::getModel('ho_pricecrawler/scrapinghub_jobs')->listJobs(array(
                'spider' => $spider->getIdentifier(),
                'state'  => 'finished',
                'count' => 1,
            ));
            if ($jobs) {
                $job = $jobs[0];
                $spider->setJob($job);
            }
        }

        return $spiders;
    }

    /**
     * Check if given job ID has logs
     *
     * @param int $jobId
     * @return bool
     */
    public function hasLogs($jobId)
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