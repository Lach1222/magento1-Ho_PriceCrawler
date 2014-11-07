<?php

class Ho_PriceCrawler_Model_System_Config_Source_Sites
{
    /**
     * @return array
     */
    public function toArray()
    {
        $sites = Mage::helper('ho_pricecrawler/sites')->getOptionsArray();

        return $sites;
    }
}
