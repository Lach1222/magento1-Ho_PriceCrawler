<?php

/**
 * Class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Jobs
 *
 * @method string getName()
 * @method array getJobs()
 */
class Ho_PriceCrawler_Block_Adminhtml_Dashboard_Jobs extends Mage_Adminhtml_Block_Dashboard_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ho/pricecrawler/jobs.phtml');
    }
}