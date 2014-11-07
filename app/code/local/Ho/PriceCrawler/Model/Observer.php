<?php

class Ho_PriceCrawler_Model_Observer
{
    /**
     * Tries to schedule all active spiders
     *
     * @param $observer
     * @return string
     */
    public function scheduleJobs($observer)
    {
        $sites = Mage::getModel('ho_pricecrawler/sites')->getActiveSites();
        $jobs = Mage::getModel('ho_pricecrawler/scrapinghub_jobs');

        $i = $errors = 0;
        foreach ($sites as $site) {
            $result = $jobs->schedule($site->getIdentifier());
            $errors += $result ? 0 : 1;
            $i++;
        }

        return $errors == 0
            ? Mage::helper('ho_pricecrawler')->__('Succesfully scheduled all %s spiders', $i)
            : Mage::helper('ho_pricecrawler')->__('Scheduled %s out of %s spiders (%s error(s))', ($i - $errors), $i, $errors);
    }

    /**
     * Import all items from the last finished job from all active spiders
     *
     * @param $observer
     * @return string
     */
    public function importItems($observer)
    {
        $sites = Mage::getModel('ho_pricecrawler/sites')->getActiveSites();

        $importMessages = array();

        foreach ($sites as $site) {
            /** @var Ho_PriceCrawler_Model_Scrapinghub_Items $import */
            $import = Mage::getModel('ho_pricecrawler/scrapinghub_items');
            $importMessages[] = $import->import($site->getIdentifier());
        }

        return implode("\n", $importMessages);
    }

    /**
     * @param $observer
     * @return array
     */
    public function importLogs($observer)
    {
        $model = Mage::getModel('ho_pricecrawler/scrapinghub_logs');

        $result = $model->import();

        return implode("\n", $result);
    }
}