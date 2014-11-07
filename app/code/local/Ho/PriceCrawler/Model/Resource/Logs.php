<?php

class Ho_PriceCrawler_Model_Resource_Logs extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ho_pricecrawler/logs', 'entity_id');
    }
}