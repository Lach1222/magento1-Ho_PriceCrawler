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

    /**
     * @param Varien_Event_Observer $observer
     */
    public function showLogsInfo($observer)
    {
        /** @var Mage_Core_Controller_Request_Http $request */
        $request = $observer->getEvent()->getControllerAction()->getRequest();

        if ($request->getModuleName() != 'ho_pricecrawler' || $request->getControllerName() != 'adminhtml_logs') return;

        $messages = array();
        $messages[] = "With these logs, you can easily check which requests had no item scraped. Select a Job ID and set 'Has Item' to 'No', to show all the requests without items.";
        $messages[] = "Copy the URL of the request and look it up in the 'Requests' tab of the job in Scrapinghub to add a template, or add (a part of) the URL to the Exclude Patterns.";

        foreach ($messages as $message) {
            Mage::getSingleton('core/session')->addNotice(Mage::helper('ho_pricecrawler')->__($message));
        }
    }
}