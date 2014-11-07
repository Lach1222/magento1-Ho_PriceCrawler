<?php

class Ho_PriceCrawler_Helper_Sites extends Mage_Core_Helper_Abstract
{
    public function getOptionsArray()
    {
        $sites = Mage::getModel('ho_pricecrawler/sites')->getActiveSites();

        $options = array();
        foreach ($sites as $site) {
            $options[$site->getId()] = $site->getName();
        }

        return $options;
    }
}