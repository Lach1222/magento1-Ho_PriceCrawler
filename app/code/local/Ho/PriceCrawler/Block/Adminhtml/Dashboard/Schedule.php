<?php

class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Schedule extends Mage_Adminhtml_Block_Dashboard_Abstract
{
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
}